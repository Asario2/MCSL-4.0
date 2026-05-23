<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\hackinglogService;

class RequestInspectionMiddleware
{
    protected int $maxScore = 15;

    protected int $maxInputLength = 5000;

    protected int $maxStoredMatches = 20;

    protected hackinglogService $hackinglogService;

    protected array $patterns = [

        /*
        |--------------------------------------------------------------------------
        | XSS
        |--------------------------------------------------------------------------
        */

        '/<\s*script\b/i'              => 15,
        '/<\s*svg\b/i'                 => 15,
        '/javascript\s*:/i'            => 10,
        '/\bon\w+\s*=/i'               => 8,
        '/document\.cookie/i'          => 10,
        '/window\.location/i'          => 8,
        '/<\s*iframe\b/i'              => 12,

        /*
        |--------------------------------------------------------------------------
        | SQL Injection
        |--------------------------------------------------------------------------
        */

        '/union\s+select/i'            => 15,
        '/\bselect\b.+\bfrom\b/i'      => 8,
        '/\binformation_schema\b/i'    => 15,
        '/\bsleep\s*\(/i'              => 15,
        '/\bbenchmark\s*\(/i'          => 15,
        '/or\s+1\s*=\s*1/i'            => 12,
        '/--/'                         => 5,

        /*
        |--------------------------------------------------------------------------
        | Command Injection
        |--------------------------------------------------------------------------
        */

        '/;\s*(rm|cat|wget|curl)\s+/i' => 20,
        '/\$\(/'                       => 15,
        '/`.+`/'                       => 15,
        '/\|\s*(sh|bash|powershell)/i' => 20,

        /*
        |--------------------------------------------------------------------------
        | Path Traversal
        |--------------------------------------------------------------------------
        */

        '/\.\.\//i'                    => 10,
        '/\.\.\\\\/i'                  => 10,
        '/\/etc\/passwd/i'             => 20,
        '/boot\.ini/i'                 => 20,

        /*
        |--------------------------------------------------------------------------
        | PHP Injection
        |--------------------------------------------------------------------------
        */

        '/<\?(php)?/i'                 => 20,

        /*
        |--------------------------------------------------------------------------
        | Encoded Attacks
        |--------------------------------------------------------------------------
        */

        '/\\\\x[0-9a-fA-F]{2}/'        => 10,
        '/%[0-9a-fA-F]{2}/'            => 5,
    ];

    public function __construct(hackinglogService $hackinglogService)
    {
        $this->hackinglogService = $hackinglogService;
    }
    protected function flattenInputs(array $inputs, string $prefix = ''): array
    {
        $result = [];

        foreach ($inputs as $key => $value) {

            $newKey = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value)) {

                $result += $this->flattenInputs($value, $newKey);

            } else {

                $result[$newKey] = $value;
            }
        }

        return $result;
    }
    public function handle(Request $request, Closure $next)
    {
        if (str_contains($request->path(), 'countpixel') && $request->filled('url') && $request->filled('route') && count($request->query()) === 2)
        {
            return $next($request);
        }

        if ($this->hackinglogService->isBanned($request->ip())) {

                abort(403, 'IP is banned.');
            }


        $result = $this->inspectRequest($request);

        $score = $result['score'];
        $matches = $result['matches'];

        /*
        |--------------------------------------------------------------------------
        | Skip clean requests
        |--------------------------------------------------------------------------
        */

        if ($score <= 0) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Current accumulated score
        |--------------------------------------------------------------------------
        */

        $currentScore = $this->hackinglogService
            ->getCurrentScore($request->ip());

        $newScore = $currentScore + $score;

        /*
        |--------------------------------------------------------------------------
        | Instant Ban
        |--------------------------------------------------------------------------
        */

        if ($newScore >= $this->maxScore) {

            $banUntil = $this->hackinglogService
                ->banIp($request,$request->ip(),$newScore,$matches);

            // $this->hackinglogService->logHit(
            //     $request->ip(),
            //     $request,
            //     $score,
            //     $matches
            // );
            $banUntil = Carbon::parse($banUntil)
                ->format('d.m.Y H:i:s');
            abort(
                403,
                "Request blocked. IP banned until {$banUntil}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Log suspicious request
        |--------------------------------------------------------------------------
        */

        $this->hackinglogService->logHit(
            $request->ip(),
            $request,
            $score,
            $matches
        );

        return $next($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Public Score Helper
    |--------------------------------------------------------------------------
    */

    public function getScore($ip, Request $request): int
    {
        return $this->inspectRequest($request)['score'];
    }

    /*
    |--------------------------------------------------------------------------
    | Main Inspection Logic
    |--------------------------------------------------------------------------
    */

    protected function inspectRequest(Request $request): array
{
    $score = 0;

    $matches = [];

    /*
    |--------------------------------------------------------------------------
    | Collect Inputs
    |--------------------------------------------------------------------------
    */

    $inputs = array_merge(
        $request->query(),
        $request->post(),
        is_array($request->json()?->all())
            ? $request->json()->all()
            : []
    );

    /*
    |--------------------------------------------------------------------------
    | Flatten Nested Arrays
    |--------------------------------------------------------------------------
    */

    $inputs = $this->flattenInputs($inputs);

    /*
    |--------------------------------------------------------------------------
    | Scan Inputs
    |--------------------------------------------------------------------------
    */

    foreach ($inputs as $key => $value) {

        $value = $this->normalizeValue($value);

        if ($value === '') {
            continue;
        }

        foreach ($this->patterns as $pattern => $points) {

            if (preg_match($pattern, $value)) {

                $score += $points;

                if (count($matches) < $this->maxStoredMatches) {

                    $matches[] = [
                        'source'  => $key,
                        'pattern' => $pattern,
                        'value'   => mb_substr($value, 0, 200),
                        'points'  => $points,
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Base64 Attack Detection
        |--------------------------------------------------------------------------
        */

        if ($this->looksLikeBase64($value)) {

            $decoded = base64_decode($value, true);

            if ($decoded !== false) {

                $decoded = $this->recursiveDecode($decoded);

                foreach ($this->patterns as $pattern => $points) {

                    if (preg_match($pattern, $decoded)) {

                        $score += ($points + 5);

                        if (count($matches) < $this->maxStoredMatches) {

                            $matches[] = [
                                'source'  => $key . '_base64',
                                'pattern' => $pattern,
                                'value'   => mb_substr($decoded, 0, 200),
                                'points'  => $points + 5,
                            ];
                        }
                    }
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Limit Maximum Score
    |--------------------------------------------------------------------------
    */

    $score = min($score, 999);

    return [
        'score'   => $score,
        'matches' => $matches,
    ];
}

    /*
    |--------------------------------------------------------------------------
    | Normalize Input
    |--------------------------------------------------------------------------
    */

    protected function normalizeValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $value = (string)$value;

        $value = mb_substr($value, 0, $this->maxInputLength);

        $value = $this->recursiveDecode($value);

        return trim($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Recursive URL Decode
    |--------------------------------------------------------------------------
    */

    protected function recursiveDecode(string $value): string
    {
        do {

            $decoded = urldecode($value);

            if ($decoded === $value) {
                break;
            }

            $value = $decoded;

        } while (true);

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Base64 Detection
    |--------------------------------------------------------------------------
    */

    protected function looksLikeBase64(string $value): bool
    {
        if (strlen($value) < 16) {
            return false;
        }

        return preg_match(
            '/^[A-Za-z0-9\/\r\n+]*={0,2}$/',
            $value
        ) === 1;
    }
}
