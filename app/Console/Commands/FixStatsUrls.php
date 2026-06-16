<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStatsUrls extends Command
{
    protected $signature = 'stats:fix-urls';

    protected $description = 'Entfernt führende / aus URLs außer bei "/"';

    public function handle()
    {
        $rows = DB::connection('mariadb')
            ->table('xgen_page_views')
            ->where('url', '!=', '/')
            ->where('url', 'LIKE', '/%')
            ->get(['id', 'url']);

        $count = 0;

        foreach ($rows as $row) {

            $newUrl = ltrim($row->url, '/');

            DB::connection('mariadb')
                ->table('xgen_page_views')
                ->where('id', $row->id)
                ->update([
                    'url' => $newUrl
                ]);

            $count++;
        }

        $this->info("{$count} URLs aktualisiert.");

        return self::SUCCESS;
    }
}
