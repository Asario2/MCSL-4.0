<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Normalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\hackinglogService;

class RequestInspectionMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */

    protected int $maxScore = 15;

    protected int $maxInputLength = 10000;

    protected int $maxStoredMatches = 50;

    protected int $maxRequestsPerMinute = 120;

    protected hackinglogService $hackinglogService;

    /*
    |--------------------------------------------------------------------------
    | Ignored Keys
    |--------------------------------------------------------------------------
    */

    protected array $ignoredInputKeys = [

        'headers.accept',
        'headers.accept-language',
        'headers.sec-fetch-site',
        'headers.sec-fetch-mode',
        'headers.sec-fetch-dest',
        'headers.sec-fetch-user',
        'headers.sec-ch-ua',
        'headers.sec-ch-ua-mobile',
        'headers.sec-ch-ua-platform',
        'headers.priority',
        'headers.connection',
        'headers.host',
        'headers.content-length',
        'headers.cookie',
    ];

    /*
    |--------------------------------------------------------------------------
    | Skip Entropy Keys
    |--------------------------------------------------------------------------
    */

    protected array $skipEntropyKeys = [

        'headers.',
        'server.user_agent',
        'server.referer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Honeypot Fields
    |--------------------------------------------------------------------------
    */

    protected array $honeypotFields = [

        'website',
        'homepage',
        // 'url',
        'nickname',
    ];

    /*
    |--------------------------------------------------------------------------
    | Detection Patterns
    |--------------------------------------------------------------------------
    */

    protected array $patterns = [

        /*
        |--------------------------------------------------------------------------
        | XSS
        |--------------------------------------------------------------------------
        */

        '/<\s*script\b/i'                    => 15,
        '/<\s*svg\b/i'                       => 12,
        '/<\s*iframe\b/i'                    => 15,
        '/javascript\s*:/i'                 => 12,
        '/vbscript\s*:/i'                   => 12,
        '/data\s*:\s*text\/html/i'          => 12,
        '/\bon\w+\s*=/i'                    => 8,
        '/document\.cookie/i'               => 10,
        '/window\.location/i'               => 10,
        '/alert\s*\(/i'                     => 8,
        '/eval\s*\(/i'                      => 12,

        /*
        |--------------------------------------------------------------------------
        | SQL Injection
        |--------------------------------------------------------------------------
        */

        '/union\s+select/i'                 => 20,
        '/\bselect\b.+\bfrom\b/i'           => 8,
        '/\binformation_schema\b/i'         => 20,
        '/\bsleep\s*\(/i'                   => 20,
        '/\bbenchmark\s*\(/i'               => 20,
        '/\bload_file\s*\(/i'               => 20,
        '/into\s+outfile/i'                 => 20,
        '/or\s+1\s*=\s*1/i'                 => 15,
        '/and\s+1\s*=\s*1/i'                => 10,
        '/\s--(\s|$)/'                      => 5,

        /*
        |--------------------------------------------------------------------------
        | Command Injection
        |--------------------------------------------------------------------------
        */

        '/;\s*(rm|cat|wget|curl|bash|sh)\s+/i' => 25,
        '/\|\s*(bash|sh|powershell)/i'         => 25,
        '/\$\(/'                               => 20,
        '/(?:^|[\s;|&])`[^`\n]{1,200}`(?:$|[\s;|&])/i' => 20,
        '/nc\s+-e/i'                           => 30,

        /*
        |--------------------------------------------------------------------------
        | Path Traversal
        |--------------------------------------------------------------------------
        */

        '/\.\.\//i'                         => 15,
        '/\.\.\\\\/i'                      => 15,
        '/\/etc\/passwd/i'                 => 30,
        '/boot\.ini/i'                     => 30,
        '/\/proc\/self/i'                  => 20,
        '/\.env/i'                         => 20,

        /*
        |--------------------------------------------------------------------------
        | PHP Injection
        |--------------------------------------------------------------------------
        */

        '/<\?(php)?/i'                     => 30,
        '/php:\/\/input/i'                 => 20,
        '/base64_decode\s*\(/i'            => 15,

        /*
        |--------------------------------------------------------------------------
        | Upload Attacks
        |--------------------------------------------------------------------------
        */

        '/\.(php|phtml|phar)\./i'          => 30,
        '/shell\.php/i'                    => 40,
        '/cmd\.php/i'                      => 40,
        '/backdoor/i'                      => 40,

        /*
        |--------------------------------------------------------------------------
        | SSRF
        |--------------------------------------------------------------------------
        */

        '/127\.0\.0\.1/i'                  => 20,
        // '/localhost/i'                     => 5,
        '/169\.254\./i'                    => 25,
        '/file:\/\//i'                     => 25,
        '/gopher:\/\//i'                   => 30,

        /*
        |--------------------------------------------------------------------------
        | Scanner / Recon
        |--------------------------------------------------------------------------
        */

        '/phpmyadmin/i'                    => 15,
        '/vendor\/phpunit/i'               => 20,
        '/cgi-bin/i'                       => 15,

        /*
        |--------------------------------------------------------------------------
        | Scanner User Agents
        |--------------------------------------------------------------------------
        */

        '/sqlmap/i'                        => 50,
        '/nikto/i'                         => 40,
        '/acunetix/i'                      => 40,
        '/nmap/i'                          => 30,
        '/masscan/i'                       => 30,
        '/python-requests/i'               => 15,

        /*
        |--------------------------------------------------------------------------
        | Encoded Attacks
        |--------------------------------------------------------------------------
        */

        '/\\\\x[0-9a-fA-F]{2}/'            => 10,
        '/%[0-9a-fA-F]{2}/'                => 5,
    ];

    public function __construct(
        hackinglogService $hackinglogService
    ) {
        $this->hackinglogService = $hackinglogService;
    }

    /*
    |--------------------------------------------------------------------------
    | Middleware Handle
    |--------------------------------------------------------------------------
    */

    public function handle(Request $request, Closure $next)
    {

        if ($request->is('admin/email')) {
             return $next($request);
        }
        /*
        |--------------------------------------------------------------------------
        | Dangerous HTTP Methods
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                strtoupper($request->method()),
                ['TRACE', 'TRACK', 'DEBUG']
            )
        ) {
            abort(403, 'Dangerous HTTP method blocked.');
        }

        /*
        |--------------------------------------------------------------------------
        | Honeypot Fields
        |--------------------------------------------------------------------------
        */

        foreach (
            $this->honeypotFields
            as $field
        ) {

            if ($request->filled($field)) {

                abort(
                    403,
                    'Honeypot triggered.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Velocity Detection
        |--------------------------------------------------------------------------
        */

        $rateKey = 'ids_rate_' . $request->ip();

        $requestCount = Cache::increment(
            $rateKey
        );

        Cache::put(
            $rateKey,
            $requestCount,
            60
        );

        if (
            $requestCount >
            $this->maxRequestsPerMinute
        ) {

            abort(
                429,
                'Too many requests.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Honeypot / Recon Paths
        |--------------------------------------------------------------------------
        */

        $honeypots = [

            '/.env',
            '/.git/config',
            '/phpmyadmin',
            '/adminer.php',
            '/vendor/phpunit',
        ];

        foreach ($honeypots as $honeypot) {

            if (
                str_contains(
                    strtolower($request->path()),
                    strtolower($honeypot)
                )
            ) {

                $this->hackinglogService->banIp(
                    $request,
                    $request->ip(),
                    999,
                    [[
                        'source'  => 'honeypot',
                        'pattern' => $honeypot,
                        'value'   => $request->path(),
                        'points'  => 999,
                    ]]
                );

                abort(
                    403,
                    'Recon attack detected.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Already banned?
        |--------------------------------------------------------------------------
        */

        if (
            $this->hackinglogService->isBanned(
                $request->ip()
            )
        ) {
            abort(403, 'IP is banned.');
        }

        /*
        |--------------------------------------------------------------------------
        | Countpixel reduced sensitivity
        |--------------------------------------------------------------------------
        */

        $reducedSensitivity =
            str_contains($request->path(), 'countpixel')
            && $request->filled('url')
            && $request->filled('route')
            && count($request->query()) <= 3;

        /*
        |--------------------------------------------------------------------------
        | Inspect Request
        |--------------------------------------------------------------------------
        */

        $result = $this->inspectRequest(
            $request,
            $reducedSensitivity
        );

        $score = $result['score'];

        $matches = $result['matches'];

        /*
        |--------------------------------------------------------------------------
        | Ignore empty matches
        |--------------------------------------------------------------------------
        */

        if (
            count($matches) === 0
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Trust Score
        |--------------------------------------------------------------------------
        */

        $trust = $this->calculateTrust(
            $request
        );

        $score -= $trust;

        $score = max(0, $score);

        /*
        |--------------------------------------------------------------------------
        | Clean Request
        |--------------------------------------------------------------------------
        */

        if ($score <= 0) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Current Score
        |--------------------------------------------------------------------------
        */

        $currentScore = $this->hackinglogService
            ->getCurrentScore($request->ip());

        $newScore = $currentScore + $score;

        /*
        |--------------------------------------------------------------------------
        | Ban IP
        |--------------------------------------------------------------------------
        */

        if ($newScore >= $this->maxScore) {

            $banUntil = $this->hackinglogService
                ->banIp(
                    $request,
                    $request->ip(),
                    $newScore,
                    $matches
                );

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
    | Main Inspection
    |--------------------------------------------------------------------------
    */

    protected function inspectRequest(
        Request $request,
        bool $reducedSensitivity = false
    ): array {
            /*
        |--------------------------------------------------------------------------
        | Ignore internal countpixel requests
        |--------------------------------------------------------------------------
        */

        if (str_contains($request->path(), 'countpixel')
            && $request->filled('url')
            && $request->filled('route'))
        {

            return [

                'score'   => 0,

                'matches' => [],
            ];
        }
        $score = 0;

        $matches = [];

        $inputs = $this->flattenInputs(
            $this->collectInputs($request)
        );

        foreach ($inputs as $key => $value) {

            /*
            |--------------------------------------------------------------------------
            | Ignore noisy headers
            |--------------------------------------------------------------------------
            */

            foreach (
                $this->ignoredInputKeys
                as $ignored
            ) {

                if (
                    str_starts_with(
                        $key,
                        $ignored
                    )
                ) {
                    continue 2;
                }
            }

            $value = $this->normalizeValue(
                $value
            );

            /*
            |--------------------------------------------------------------------------
            | Ignore empty payloads
            |--------------------------------------------------------------------------
            */

            if (
                $value === ''
                || $value === '[]'
                || $value === '{}'
                || $value === 'null'
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Ignore tiny values
            |--------------------------------------------------------------------------
            */

            if (
                mb_strlen($value) <= 1
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Entropy Detection
            |--------------------------------------------------------------------------
            */

            $skipEntropy = false;

            foreach (
                $this->skipEntropyKeys
                as $skipKey
            ) {

                if (
                    str_starts_with(
                        $key,
                        $skipKey
                    )
                ) {

                    $skipEntropy = true;

                    break;
                }
            }

            if (
                !$skipEntropy
                && strlen($value) > 120
                && $this->shannonEntropy($value) > 5.2
            ) {

                $score += 10;

                $matches[] = [

                    'source'  => $key,

                    'pattern' => 'ENTROPY',

                    'value'   => mb_substr(
                        $value,
                        0,
                        300
                    ),

                    'points'  => 10,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Heuristics
            |--------------------------------------------------------------------------
            */

            if (
                substr_count($value, '%') > 15
            ) {
                $score += 5;
            }

            if (
                substr_count(
                    $value,
                    '../'
                ) > 3
            ) {
                $score += 15;
            }

            if (
                strlen($value) > 3000
            ) {
                $score += 5;
            }

            /*
            |--------------------------------------------------------------------------
            | Regex Scan
            |--------------------------------------------------------------------------
            */

            foreach (
                $this->patterns
                as $pattern => $points
            ) {

                preg_match_all(
                    $pattern,
                    $value,
                    $found
                );

                if (
                    !empty($found[0])
                ) {

                    $matchCount = count(
                        $found[0]
                    );

                    $calculatedPoints =
                        $points * $matchCount;

                    if (
                        $reducedSensitivity
                    ) {

                        $calculatedPoints =
                            (int)ceil(
                                $calculatedPoints / 2
                            );
                    }

                    $score +=
                        $calculatedPoints;

                    if (
                        count($matches)
                        < $this->maxStoredMatches
                    ) {

                        $matches[] = [

                            'source' =>
                                $key,

                            'pattern' =>
                                $pattern,

                            'match_count' =>
                                $matchCount,

                            'value' =>
                                mb_substr(
                                    $value,
                                    0,
                                    300
                                ),

                            'points' =>
                                $calculatedPoints,
                        ];
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SQL Combo Detection
            |--------------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/select/i',
                    $value
                )
                &&
                preg_match(
                    '/sleep\s*\(/i',
                    $value
                )
            ) {

                $score += 30;

                $matches[] = [

                    'source'  => $key,

                    'pattern' => 'SQL_COMBO',

                    'value'   => mb_substr(
                        $value,
                        0,
                        300
                    ),

                    'points'  => 30,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Base64 Detection
            |--------------------------------------------------------------------------
            */

            if (
                $this->looksLikeBase64(
                    $value
                )
            ) {

                $decoded = base64_decode(
                    $value,
                    true
                );

                if ($decoded !== false) {

                    $decoded =
                        $this->recursiveDecode(
                            $decoded
                        );

                    if (
                        trim($decoded) === ''
                        || trim($decoded)
                            === '[]'
                        || trim($decoded)
                            === '{}'
                    ) {
                        continue;
                    }

                    foreach (
                        $this->patterns
                        as $pattern => $points
                    ) {

                        if (
                            preg_match(
                                $pattern,
                                $decoded
                            )
                        ) {

                            $extraPoints =
                                $points + 10;

                            $score +=
                                $extraPoints;

                            if (
                                count($matches)
                                < $this->maxStoredMatches
                            ) {

                                $matches[] = [

                                    'source' =>
                                        $key
                                        . '_base64',

                                    'pattern' =>
                                        $pattern,

                                    'value' =>
                                        mb_substr(
                                            $decoded,
                                            0,
                                            300
                                        ),

                                    'points' =>
                                        $extraPoints,
                                ];
                            }
                        }
                    }
                }
            }
        }

        $score = min($score, 999);

        return [

            'score'   => $score,

            'matches' => $matches,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Trust Score
    |--------------------------------------------------------------------------
    */

    protected function calculateTrust(
        Request $request
    ): int {

        $trust = 0;

        if (
            $request->header(
                'accept-language'
            )
        ) {
            $trust += 2;
        }

        if (
            $request->header(
                'sec-fetch-site'
            )
        ) {
            $trust += 3;
        }

        if (
            $request->header(
                'sec-ch-ua'
            )
        ) {
            $trust += 3;
        }

        if (
            str_contains(
                strtolower(
                    $request->userAgent()
                ),
                'mozilla'
            )
        ) {
            $trust += 5;
        }

        return $trust;
    }

    /*
    |--------------------------------------------------------------------------
    | Shannon Entropy
    |--------------------------------------------------------------------------
    */

    protected function shannonEntropy(
        string $string
    ): float {

        $h = 0;

        $len = strlen($string);

        if ($len === 0) {
            return 0;
        }

        foreach (
            count_chars($string, 1)
            as $count
        ) {

            $p = $count / $len;

            $h -= $p * log($p, 2);
        }

        return $h;
    }

    /*
    |--------------------------------------------------------------------------
    | Collect Inputs
    |--------------------------------------------------------------------------
    */

    protected function collectInputs(
        Request $request
    ): array {

        $files = [];

        foreach (
            $request->allFiles()
            as $key => $file
        ) {

            $filename =
                $file->getClientOriginalName();

            /*
            |--------------------------------------------------------------------------
            | Dangerous upload extension
            |--------------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/\.(php|phtml|phar)$/i',
                    $filename
                )
            ) {

                abort(
                    403,
                    'Dangerous file upload.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Upload content scan
            |--------------------------------------------------------------------------
            */

            $content =
                @file_get_contents(
                    $file->getRealPath()
                );

            if (
                $content !== false
            ) {

                if (
                    preg_match(
                        '/<\?php/i',
                        $content
                    )
                ) {

                    abort(
                        403,
                        'PHP payload detected.'
                    );
                }
            }

            $files[$key] = [

                'name' => $filename,

                'mime' =>
                    $file->getMimeType(),
            ];
        }

        return [

            'query' =>
                $request->query(),

            'post' =>
                $request->post(),

            'json' => is_array(
                $request->json()?->all()
            )
                ? $request
                    ->json()
                    ->all()
                : [],

            'headers' =>
                $request->headers->all(),

            'server' => [

                'user_agent' =>
                    $request->userAgent(),

                'referer' =>
                    $request->headers->get(
                        'referer'
                    ),

                'path' =>
                    $request->path(),

                'method' =>
                    $request->method(),
            ],

            'files' => $files,

            'raw' =>
                $request->getContent(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Flatten Arrays
    |--------------------------------------------------------------------------
    */

    protected function flattenInputs(
        array $inputs,
        string $prefix = ''
    ): array {

        $result = [];

        foreach (
            $inputs as $key => $value
        ) {

            $newKey = $prefix
                ? $prefix . '.'
                    . $key
                : $key;

            if (is_array($value)) {

                $result +=
                    $this->flattenInputs(
                        $value,
                        $newKey
                    );

            } else {

                $result[$newKey]
                    = $value;
            }
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Value
    |--------------------------------------------------------------------------
    */

    protected function normalizeValue(
        mixed $value
    ): string {

        if (is_array($value)) {

            $value = json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            );
        }

        $value = (string)$value;

        $value = mb_substr(
            $value,
            0,
            $this->maxInputLength
        );

        /*
        |--------------------------------------------------------------------------
        | Unicode normalize
        |--------------------------------------------------------------------------
        */

        if (
            class_exists(
                Normalizer::class
            )
        ) {

            $value =
                normalizer_normalize(
                    $value,
                    Normalizer::FORM_KC
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Lowercase
        |--------------------------------------------------------------------------
        */

        $value = mb_strtolower(
            $value
        );

        /*
        |--------------------------------------------------------------------------
        | Recursive decode
        |--------------------------------------------------------------------------
        */

        $value =
            $this->recursiveDecode(
                $value
            );

        return trim($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Recursive Decode
    |--------------------------------------------------------------------------
    */

    protected function recursiveDecode(
        string $value
    ): string {

        do {

            $old = $value;

            $value =
                urldecode($value);

            $value =
                html_entity_decode(
                    $value,
                    ENT_QUOTES
                    | ENT_HTML5,
                    'UTF-8'
                );

        } while (
            $old !== $value
        );

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Base64 Detection
    |--------------------------------------------------------------------------
    */

    protected function looksLikeBase64(
        string $value
    ): bool {

        if (
            strlen($value) < 32
        ) {
            return false;
        }

        if (
            strlen($value) % 4 !== 0
        ) {
            return false;
        }

        $decoded = base64_decode(
            $value,
            true
        );

        if (
            $decoded === false
        ) {
            return false;
        }

        return
            base64_encode(
                $decoded
            ) === $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Public Score Helper
    |--------------------------------------------------------------------------
    */

    public function getScore(
        $ip,
        Request $request
    ): int {

        return
            $this->inspectRequest(
                $request
            )['score'];
    }
}
