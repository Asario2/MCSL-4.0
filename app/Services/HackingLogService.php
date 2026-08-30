<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class hackinglogService
{
    /**
     * Score ab dem gebannt wird
     */
    protected int $maxScore = 15;

    /**
     * Score ab dem gebannt wird (zusätzlich)
     */
    protected int $blockThreshold = 15;

    /**
     * Ban-Dauer in Stunden je Verstoß-Stufe
     */
    protected array $banDurations = [
        1 => 1,
        2 => 2,
        3 => 10,
        4 => 48,
        5 => 300,
        6 => 1000,
        7 => 2000,
        8 => 5000,
        9 =>  90000,
        10 => 99000.
    ];
    public function banIp(Request $request,string $ip,int $score,$matches)
    {
        // \Log::info($request->all());
        $viol = DB::connection("mariadb")->table("xgen_hackinglog")->where("ip",$ip)->where("dom",SD())->orderBy("id","DESC")->value("violations");
        $viol = $viol ?? 0;
        $viol++;
        $banUntil = now()->addHours($this->banDurations[$viol]);

        DB::connection("mariadb")->table('xgen_hackinglog')->insert(

            [
                "dom" => SD(),
                'method'       => $request->method(),
                "url" =>$request->fullUrl(),
                'banned_until' => $banUntil,
                "violations" => $viol,
                'score'        => $score,
                "ip"=>$ip,
                'created_at'   => now(),
                'matches'      => json_encode($matches, JSON_PRETTY_PRINT),
                'agent'        => $request->userAgent(),
            ]
        );

        return $banUntil;
    }
    /**
     * Hauptfunktion: speichert jeden Hit, berechnet violations & banned_until
     */
    public function logHit(string $ip, Request $request, int $score, array $matches): ?Carbon
{
    $now = now();

    // Zähle bisherige Verstöße (Score > 0)
    $violations = DB::connection("mariadb")->table('xgen_hackinglog')
        ->where('ip', $ip)
        ->where('dom', SD())
        ->where('score', '>', 0)
        ->count() + 1;
    $viol = $violations;
    $violations = $this->violations($request->ip());
    $violations = min($violations, 8);
    // \Log::info("SC:".$score."MS:".$this->maxScore);
    // Ban-Dauer berechnen, auch wenn bereits Ban aktiv
    $hours = $this->banDurations[$violations] ?? 10;

    $banUntil = ($score >= $this->maxScore)
        ? $now->copy()->addHours($hours)
        : $this->GetBannedUntil($ip);
    if($score < 1 && $score != -1)
    {
        return now();
    }
    if($score > $this->maxScore)
    {

    DB::connection("mariadb")->table('xgen_hackinglog')->insert([
            'ip'           => $ip,
            'dom'          => SD(),
            'url'          => $request->fullUrl(),
            'method'       => $request->method(),
            'score'        => $score,
            'matches'      => json_encode($matches, JSON_PRETTY_PRINT),
            'agent'        => $request->userAgent(),
            'violations'   => $violations,
            'banned_until' => $banUntil,
            'created_at'   => $now,
        ]);
    }
    // Immer einen neuen Eintrag machen


    return $banUntil;
}
public function getCurrentScore(string $ip): int
{
    return (int) DB::connection("mariadb")->table('xgen_hackinglog')
        ->where('ip', $ip)
        ->where('created_at', '>=', now()->subHours(24))
        ->sum('score');
}

    /**
     * Prüft, ob eine IP aktuell gebannt ist
     */
    public function isBanned(string $ip): bool
    {
        $record = DB::connection("mariadb")->table('xgen_hackinglog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->orderByDesc('banned_until')
            ->first();

        return (bool) $record;
    }

    /**
     * Liefert das aktuell gültige banned_until (oder null)
     */
    public function bannedTill(string $ip): ?Carbon
    {
        $row = DB::connection("mariadb")->table('xgen_hackinglog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->orderByDesc('banned_until')
            ->first();

        return $row ? Carbon::parse($row->banned_until) : null;
    }

    /**
     * Anzahl aller Verstöße einer IP
     */
    public function violations(string $ip): int
    {
       $last = DB::connection("mariadb")->table('xgen_hackinglog')
        ->where('ip', $ip)
        ->where('dom', SD())
        ->orderByDesc('violations')
        ->select('violations')
        ->first();

        $violations = $last ? $last->violations + 1 : 1; // falls kein Eintrag existiert, start bei 1

        return $violations;
    }

    /**
     * Prüft, ob Score hoch genug für Block ist
     */
    public function shouldBlock(int $score): bool
    {
        return $score >= $this->blockThreshold;
    }

    /**
     * Löscht alte Logs (Cron)
     */
    public function cleanup(int $days = 14): int
    {
        return '';
        return DB::connection("mariadb")->table('xgen_hackinglog')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
    function GetBannedUntil($ip){
    $now = now();
    $viol = DB::connection('mariadb')->table("xgen_hackinglog")->where("ip",$ip)->where("dom",SD())->select("violations")->orderByDesc("violations")->first();
    $hours = $this->banDurations[(@$viol->violations+1)] ?? 10;
    return $now->copy()->addHours($hours);

    }
    /**
     * Optional: alle Hits einer IP abrufen
     */
    public function getHits(string $ip)
    {
        return DB::connection("mariadb")->table('xgen_hackinglog')
            ->where('ip', $ip)
            ->where('dom', SD())
            ->orderByDesc('created_at')
            ->get();
    }
}
