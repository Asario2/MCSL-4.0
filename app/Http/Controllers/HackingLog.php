<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class hackinglog extends Controller
{
    //
    // public static function save($data)
    // {
    //     // 'ip'      => $request->ip(),
    //     //         'url'     => $request->fullUrl(),
    //     //         'method'  => $request->method(),
    //     //         'score'   => $score,
    //     //         'matches' => $matches,
    //     //         'agent'   => $request->userAgent(),
    //     DB::connection("mariadb")->table('xgen_hackinglog')->insert([
    //         'ip'         => (string) $data['ip'],
    //         "dom"        => SD(),
    //         'url'        => (string) $data['url'],
    //         'method'     => (string) $data['method'],
    //         'score'      => (int) $data['score'],
    //         'matches'    => is_array($data['matches']) ? json_encode($data['matches'],JSON_PRETTY_PRINT) : $data['matches'],
    //         'agent'      => (string) $data['agent'],
    //         'created_at' => now(),
    //         "banned_untill"=>$data['banned_untill']
    //     ]);

    // }
    public function show()
    {
        if(!CheckZrights("hackinglog"))
        {
            return redirect('/no-rights');
        }
            $data = DB::connection('mariadb')->table("xgen_hackinglog")
                ->select(
                'xgen_hackinglog.*'

            )->orderBy("id","DESC")->get();
            $data->transform(function ($item) {
                // $item->created_at = date("d.m.Y H:i:s", strtotime($item->created_at));
                // $item->banned_untill = date("d.m.Y H:i:s", strtotime($item->banned_untill));
                $item->url = rawurldecode($item->url);
                return $item;
            });
            // dd(Inertia::render('Admin/HackingLog', [
            //     'tables' => $data,
            //     'breadcrumbs' => [
            //         'MCSL IDS - Hacking Log' => route('admin.hackinglog'),
            //     ],
            // ]));

            $data = $data->map(function ($row) {

                return collect($row)->map(function ($value) {

                    if (is_string($value)) {

                        return iconv(
                            'UTF-8',
                            'UTF-8//IGNORE',
                            $value
                        );
                    }

                    return $value;

                });

            });

            return Inertia::render('Admin/HackingLog', [
                'tables' => $data->toArray(),
                'kbperpage'=>$KBperPage,
                'breadcrumbs' => [
                    'MCSL IDS - Hacking Log' => route('admin.hackinglog'),
                ],
            ]);
        return Inertia::render('Admin/HackingLog', [
            "tables"=>$data,
             'breadcrumbs' => [
            'MCSL IDS - Hacking Log' => route('admin.hackinglog'),
        ],
        ]);
    }
}
