<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUtf8Tables extends Command
{
    protected $signature = 'fix:utf8 {dom=ab}';
    protected $description = 'Korrigiert falsch codierte UTF-8 Zeichen und HTML Entities in ALLEN Tabellen';

    public function handle()
    {
        $dom = $this->argument('dom');
        $ddom = $dom !== "ab" ? "_{$dom}" : "";
        $con = "mariadb{$ddom}";

        $this->info("Starte UTF8-Fix für ALLE Tabellen auf Connection $con");

        // 🔧 Reparatur-Funktion (deine, leicht stabilisiert)
        $repair = function($str) {
            if ($str === null) return null;

            $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $search = ['Ã¼','Ã¶','Ã¤','ÃŸ','Ãœ','Ã–','Ã„'];
            $replace = ['ü','ö','ä','ß','Ü','Ö','Ä'];
            $str = str_replace($search, $replace, $str);

            $str2 = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $str);
            if ($str2 !== false) {
                $str = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $str2);
            }

            return $str;
        };

        // 🔹 Alle Tabellen holen
        $tables = DB::connection($con)->select("
            SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
        ");

        foreach ($tables as $t) {

            $table = $t->TABLE_NAME;
            $this->info("➡️ Tabelle: $table");

            // 🔹 Spalten holen
            $columns = DB::connection($con)->select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND DATA_TYPE IN ('varchar','text','mediumtext','longtext')
            ", [$table]);

            if (empty($columns)) {
                $this->warn("Keine Text-Spalten");
                continue;
            }

            $columns = collect($columns)->pluck('COLUMN_NAME')->toArray();

            // 🔹 Prüfen ob 'id' existiert
            $hasId = in_array('id', $columns) || DB::connection($con)->getSchemaBuilder()->hasColumn($table, 'id');

            if (!$hasId) {
                $this->warn("Keine ID-Spalte → übersprungen");
                continue;
            }

            // 🔹 Rows holen
            $rows = DB::connection($con)->table($table)->get();

            foreach ($rows as $row) {

                $updateData = [];

                foreach ($columns as $col) {
                    $value = $row->$col;
                    if ($value === null) continue;

                    $fixed = $repair($value);

                    if (md5($fixed) !== md5($value)) {
                        $this->line("[$table][{$row->id}] $col");
                        $this->line("ALT: " . $value);
                        $this->line("NEU: " . $fixed);
                        $this->line("-------------------");

                        $updateData[$col] = $fixed;
                    }
                }

                if (!empty($updateData)) {
                    DB::connection($con)
                        ->table($table)
                        ->where('id', $row->id)
                        ->update($updateData);
                }
            }
        }

        $this->info("✅ Alle Tabellen fertig!");
        return 0;
    }
}
