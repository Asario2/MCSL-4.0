<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\hackinglogService;

class BlockBannedIPs
{
    protected hackinglogService $hackinglog;

    public function __construct(hackinglogService $hackinglog, RequestInspectionMiddleware $relog)
    {
        $this->hackinglog = $hackinglog;
        $this->relog = $relog;
    }

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        DB::purge('mariadb');
        DB::reconnect('mariadb');

        $record = DB::connection("mariadb")
        ->table('xgen_hackinglog')
        ->where('ip', $ip)
            ->where('dom', SD())
            ->whereNotNull('banned_until')
            ->where('banned_until', '>', now())
            ->select('banned_until')
            ->orderByDesc('banned_until')
            ->first();

        if ($record) {
            $until = Carbon::parse($record->banned_until)
                ->format('d.m.Y H:i:s');


           abort(403, "Request blocked. IP banned until {$until}");
        }

        return $next($request);
    }
}
