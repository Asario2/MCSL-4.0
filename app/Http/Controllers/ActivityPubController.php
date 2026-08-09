<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityPubController extends Controller
{
    // Nur die pub-Werte laden
    public function getPub()
    {
        $logs = DB::connection('mariadb')
            ->table('xgen_activitylog')
            ->select('id', 'pub')
            ->get();

        return response()->json($logs);
    }

    // Nur pub-Werte aktualisieren
    public function updatePub(Request $request)
    {
        $payload = $request->validate([
            'ids' => 'required|array',
            'pub' => 'required|integer|in:0,1'
        ]);

        DB::connection('mariadb')
            ->table('xgen_activitylog')
            ->whereIn('id', $payload['ids'])
            ->update(['pub' => $payload['pub']]);

        return response()->json(['success' => true]);
    }
    public function checkLogs()
    {
        if (!Schema::connection("mariadb")->hasTable('xgen_activitylog')) {
            return response()->json(['success' => []]);
        }

        $logs = DB::connection("mariadb")
            ->table('xgen_activitylog')
            ->select('id', 'xkis_checked')
            ->get();

        $com = [];

        foreach ($logs as $log) {
            $com[$log->id] = $log->xkis_checked;
        }

        return response()->json([
            'success' => $com
        ]);
    }


    public function markAll()
    {
//         \Log::info("ActivityLog markAll triggered");

        $updated = DB::connection("mariadb")->table('xgen_activitylog')
            ->where('pub', 0)
            ->update([
                'pub' => 1
            ]);

        return response()->json([
            'success' => true,
            'updated_rows' => $updated
        ]);
    }
    public function mark_All(Request $request)
    {


        $data = json_decode($request->getContent(), true);



        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            \Log::error("mark_All: Keine IDs");
            return response()->json([
                'success' => false,
                'message' => 'Keine IDs empfangen'
            ]);
        }

        $updated = DB::connection('mariadb')
            ->table('xgen_activitylog')
            ->whereIn('id', $ids)
            ->update([
                'xkis_checked' => 1,
                'checked_at'   => now(),
            ]);


        return response()->json([
            'success' => true,
            'updated_rows' => $updated,
            'ids' => $ids,
        ]);
    }

    public function check_alt(Request $request)
    {
        \Log::info('check_alt');
        \Log::info($request->getContent());
        // \Log::info('check_alt aufgerufen');
        $data = json_decode($request->getContent(), true);

        $ids = $data['ids'] ?? [];
        \Log::info("ids;",$ids);
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Keine IDs empfangen.',
            ]);
        }
        // \Log::info(
        //     DB::connection('mariadb')
        //         ->table('xgen_activitylog')
        //         ->whereIn('id', $ids)
        //         ->pluck('id')
        // );
        // \Log::info(DB::connection('mariadb')->getDatabaseName());
        $updated = DB::connection('mariadb')->table('xgen_activitylog')
            ->whereIn('id', $ids)
            ->update([
                'xkis_checked' => 1,
                'checked_at'   => now(),
            ]);
        \Log::info('updated=' . $updated);
        return response()->json([
            'success' => true,
            'updated' => count($ids),
            'ids' => $ids,
        ]);
    }
    // public function check_alt(Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);

    //     return response()->json([
    //         'raw' => $request->getContent(),
    //         'data' => $data,
    //         'ids' => $data['ids'] ?? [],
    //     ]);
    // }
    public function check(Request $request)
    {
        $raw = $request->getContent();

    //     \Log::info("RAW Beacon:", [$raw]);

        $data = json_decode($raw, true);

        $ids = $data['ids'] ?? [];

        if ($ids) {
            \DB::connection("mariadb")->table('xgen_activitylog')
                ->whereIn('id', $ids)
                ->update(['pub' => 1]);
        }

        return response()->json([
            'success' => true,
            'ids' => $ids
        ]);
    }
}
