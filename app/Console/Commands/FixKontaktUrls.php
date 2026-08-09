<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixKontaktUrls extends Command
{
    protected $signature = 'fix:kontakt-urls';

    protected $description = 'Ersetzt /Kontakt durch /kontakt in blogs, texts und xgen_page_views';

    public function handle(): int
    {
        $tables = [
            'blogs',
            'texts',
            'xgen_page_views',
        ];

        foreach ($tables as $table) {

            if (!Schema::hasTable($table)) {
                $this->warn("Tabelle {$table} existiert nicht.");
                continue;
            }

            $this->info("Bearbeite {$table}...");

            $columns = Schema::getColumnListing($table);

            foreach ($columns as $column) {

                $type = Schema::getColumnType($table, $column);

                if (!in_array($type, ['string', 'text'])) {
                    continue;
                }

                $rows = DB::table($table)
                    ->where($column, 'like', '%/Kontakt%')
                    ->get(['id', $column]);

                foreach ($rows as $row) {

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update([
                            $column => str_replace('/Kontakt', '/kontakt', $row->$column),
                        ]);

                    $this->line("{$table}.{$column} -> ID {$row->id}");
                }
            }
        }

        $this->info('Fertig.');

        return self::SUCCESS;
    }
}
