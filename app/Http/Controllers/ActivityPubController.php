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
        $userId = Auth::id();

    if (!$userId) {
        // throw new \Exception('User not authenticated');
    }
    if(!Schema::hasTable("xgen_activitylog")){
        return;
    }

    // 2. Hole alle Kommentare
    $logs = DB::table('xgen_activitylog')->select('id', 'xkis_checked')->get();

    // 3. Für jeden Kommentar prüfen und ischecked aktualisieren
    foreach ($logs as $log) {

            $com[$log->id] = $log->xkis_checked;

        // // Update, falls Wert sich geändert hat
        DB::table('xgen_activitylog')
                ->where('xkis_checked', '0')
                ->update([
                    'xkis_checked' =>"1",
                    'checked_at' => now(),
                ]);

        }
        return response()->json(["success"=>$com]);
    }


    public function markAll()
    {
//         \Log::info("ActivityLog markAll triggered");

        $updated = DB::table('xgen_activitylog')
            ->where('pub', 0)
            ->update([
                'pub' => 1
            ]);

        return response()->json([
            'success' => true,
            'updated_rows' => $updated
        ]);
    }
public function check_alt(Request $request)
{
    return response()->json([
        'raw' => $request->getContent()
    ]);
}
    public function check(Request $request)
{
    $raw = $request->getContent();

//     \Log::info("RAW Beacon:", [$raw]);

    $data = json_decode($raw, true);

    $ids = $data['ids'] ?? [];

    if ($ids) {
        \DB::table('xgen_activitylog')
            ->whereIn('id', $ids)
            ->update(['pub' => 1]);
    }

    return response()->json([
        'success' => true,
        'ids' => $ids
    ]);
}
}
