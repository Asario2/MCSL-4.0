<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Settings;
use Inertia\Inertia;

class DumpgitdatabaseController extends Controller
{
    protected $ddr;
    public function __construct(Request $request)
    {
        $this->ddr = '';
        $this->connectionName = (!empty($request->dom) && $request->dom != 'ab')
            ? 'mariadb_' . $request->dom
            : 'mariadb';
        $this->mcsl_version = config('starter_eleven.version.versionnr');

    }

    public function GetFirst(Request $request)
{
    $this->connectionName = (!empty($request->dom) && $request->dom != 'ab')
        ? 'mariadb_' . $request->dom
        : 'mariadb';

    $this->usdis = $request->usdis ?? 0;
    $er = $this->usdis ? "_With_User" : "_No_User";
    $fileName = base_path('database/dumps/First_Commmit_' . ($request->dom) . $er .'.sql');

    if (!File::exists(base_path('database/dumps/'))) {
        File::makeDirectory(base_path('database/dumps/'), 0755, true);
    }

    $output  = "-- Laravel DB Dump\n";
    $output .= "-- Generated: " . now() . "\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    $tables = collect(DB::connection($this->connectionName)->select('SHOW TABLES'))
        ->map(fn($table) => array_values((array)$table)[0]);

    foreach ($tables as $table) {
        if(substr_count($table,"cleo_") || substr_count($table,"monxx_")) {
            continue;
        }

        // DROP TABLE + CREATE TABLE
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $create = DB::connection($this->connectionName)->select("SHOW CREATE TABLE `$table`");
        $output .= $create[0]->{'Create Table'} . ";\n\n";

        $connectionName = $this->connectionName;

        // Daten exportieren
        DB::connection($connectionName)
            ->table($table)
            ->orderBy(DB::raw('1'))
            ->chunk(500, function($rows) use (&$output, $table, $connectionName) {
                foreach ($rows as $row) {
                    $rowArray = (array)$row;

                    // Alle Spalten berücksichtigen, auch created_at/updated_at/published_at
                    $values = collect($rowArray)->map(function($value) use ($connectionName) {
                        if (is_null($value)) return "NULL";
                        return DB::connection($connectionName)->getPdo()->quote($value);
                    })->implode(',');

                    if (($table !== 'users' || $this->usdis) && !in_array($table, Settings::$excl_git_tables)) {
                        $output .= "INSERT INTO `$table` VALUES ($values);\n";
                    }
                }
            });

        $output .= "\n";
    }

    if(!$this->usdis) {
        $output .= $this->GetUser();
    }

    $output .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
    File::put($fileName, $output);

    \Log::info("GetFirst(): Dump erstellt für Dom {$request->dom}", ['file' => $fileName]);
    return $fileName;
}
    // Dummy User
    private function GetUser()
    {
        return "INSERT INTO `users` (`id`, `pub`, `users_rights_id`, `name`,  `password`, `email_verified_at`, `email`, `first_name`, `last_login_at`, `created_at`, `is_admin`, `profile_photo_path`, `uhash`, `updated_at`) VALUES
        (2, 1, 1, 'Developer', '\$2y\$12\$EXn0jqj9QLRxnLzSkU7DguiMc4Z10RAbwzHkn9Dh0Gp2V7bCl0vXS','2026-02-24 12:08:26', 'email@example.com', 'Devel',  '2026-02-25 12:34:39', '2026-02-25 12:07:00', 1, NULL, 'py9Q9fupN6goi52WMgghh3-s2XZb6nO48NnIZImy4EDrMQvi6JO33um5aRrSiMCb', '2026-02-25 12:36:17');";
    }

    public function ScanForNew($dom)
    {
    $path = base_path('database/dumps');
    $files = File::files($path);

    $matches = [];
    $cont = '';
    foreach ($files as $file) {

        $name = $file->getFilename();

        if (preg_match('/^Newer_Data_'.$dom.'_(.+)\.sql$/', $name, $match)) {
            $matches[] = [
                'file' => $name,
                'version' => $match[1],
                'path' => $file->getPathname()
            ];
        }
    }
    foreach($matches as $m){
    $cont .= file_get_contents($path."/".$m['file']);
    }
    return $cont;
    }

public function GetAfter(Request $request)
{
    $this->connectionName = (!empty($request->dom) && $request->dom != 'ab')
        ? 'mariadb_' . $request->dom
        : 'mariadb';
    $dom = $request->dom ?: 'ab';
    $dumpFile = base_path("database/dumps/First_Commmit_{$dom}_No_User.sql");
    $changesFile = base_path("database/dumps/Newer_Data_{$dom}_".$this->mcsl_version.".sql");
    $cfiles = $this->ScanForNew($dom);
    $addfield = @file_get_contents($changesFile) ?? '';

    if (!file_exists($dumpFile)) {
        return response()->json(['error' => 'Dump-Datei nicht gefunden.'], 404);
    }

    // 1️⃣ Dump einlesen und CREATE TABLEs extrahieren
    $dumpContent = file_get_contents($dumpFile).$cfiles;
    preg_match_all('/CREATE TABLE `(.*?)`\s*\((.*?)\)\s*ENGINE=/is', $dumpContent, $matches, PREG_SET_ORDER);

    $dumpTables = [];
    foreach ($matches as $match) {
        $tableName = $match[1];
        $columnsDef = $match[2];

        // Jede Spalte korrekt auslesen, inkl. datetime etc.
        preg_match_all('/`([^`]+)`\s+([^\n,]+)/', $columnsDef, $colMatches, PREG_SET_ORDER);
        foreach ($colMatches as $col) {
            $colName = $col[1];
            $colType = trim($col[2]);

            if (stripos($colType,'primary key') !== false ||
                stripos($colType,'key') !== false ||
                stripos($colType,'unique') !== false) {
                continue;
            }

            $dumpTables[$tableName][$colName] = $colType;
        }
    }

    // 2️⃣ Tabellen der aktuellen DB abrufen
    $tables = DB::connection($this->connectionName)->select('SHOW TABLES');
    $alterStatements = '';
    $this->changesCount = 0;
    $added_fields = [];

    foreach ($tables as $tableRow) {
        $tableName = array_values((array)$tableRow)[0];

        if (substr_count($tableName,"cleo_") || substr_count($tableName,"monxx_")) continue;
        if (!isset($dumpTables[$tableName])) continue;

        $currentColumns = DB::connection($this->connectionName)->select("SHOW COLUMNS FROM `$tableName`");

        foreach ($currentColumns as $col) {
            $colName = $col->Field;

            if (!isset($dumpTables[$tableName][$colName])) {

                $colDef = $col->Type;
                $colDef .= ($col->Null === 'NO') ? ' NOT NULL' : ' NULL';

                if (!is_null($col->Default)) {
                    $defaultVal = $col->Default;
                    if (!is_numeric($defaultVal) && strtolower($defaultVal) !== 'null') {
                        $defaultVal = "'$defaultVal'";
                    }
                    $colDef .= " DEFAULT $defaultVal";
                }

                if (!empty($col->Extra)) {
                    $colDef .= " " . $col->Extra;
                }

                $sql_q = "ALTER TABLE `$tableName` ADD COLUMN `$colName` $colDef;\n";

                if (!str_contains($addfield, $sql_q)) {
                    $alterStatements .= $sql_q;
                    $added_fields[] = [$tableName,$colName];
                    $alterStatements .= $this->alterTableCont($added_fields,$this->connectionName,$dom);
                    $this->changesCount++;
                }
            }
        }
    }

    if (!empty($alterStatements)) {
        file_put_contents($changesFile, $alterStatements, FILE_APPEND);
    }

    return response()->json([
        "dom"=>$dom,
        'message' => 'Vergleich abgeschlossen.',
        'Datei verändert' => $changesFile,
        'Summe Änderung' => $this->changesCount,
        'QueryString' => $alterStatements
    ]);
}


public function alterTableCont($arr, $connectionName, $dom)
{
    \Log::info("alterTableCont gestartet", $arr);

    $this->ddr = '';
    $addfield = file_exists(base_path("database/dumps/Newer_Data_{$dom}_".$this->mcsl_version.".sql"))
        ? file_get_contents(base_path("database/dumps/Newer_Data_{$dom}_".$this->mcsl_version.".sql"))
        : '';

    foreach ($arr as $cont) {
        $table  = $cont[0];
        $column = $cont[1];

        $rows = DB::connection($connectionName)
            ->table($table)
            ->where($column, '!=', '0')
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->get();

        foreach ($rows as $row) {
            $id = $row->id;
            $value = $row->$column;

            $valueSql = is_null($value) ? "NULL" : "'" . addslashes($value) . "'";

            $sql_q = "UPDATE `$table` SET `$column` = $valueSql WHERE `id` = '$id';";

            if (!str_contains($addfield . $this->ddr, $sql_q) && $column != 'position') {
                $this->ddr .= $sql_q . "\n";
                $this->changesCount++;
            }
        }
    }

    \Log::info("Kompletter DDR SQL:", ['sql' => $this->ddr]);
    return $this->ddr;
}


public function show(Request $request)
{
    if(!CheckZRights('Dumpgitdatabase'))
    {
        header("Location: /no-rights");
        exit;
    }
    $processed = [];
    $altres = [];

    foreach (Settings::$domss as $dom) {

        $request->merge(['dom' => $dom]);

        $FCfile = base_path("database/dumps/First_Commmit_".$dom."_No_User.sql");

        if (!file_exists($FCfile)) {
            $this->GetFirst($request);
            $processed[] = "✅ FirstDump erstellt für {$dom}";
        } else {
            $altres[] = $this->GetAfter($request);
            $processed[] = "✔ First Commit für {$dom} existiert bereits";
        }
    }

    $lupd = @file_get_contents(public_path("timespy/lastgit_DB.dat"));
    file_put_contents(public_path("timespy/lastgit_DB.dat"),now());

    // \Log::info("show() abgeschlossen", ['processed' => $processed]);
        return Inertia::render('Homepage/GitDump', [
        'data' => [
        'status' => 'success',
        'details' => $processed,
        'message'=> "Alle Tabellen aktualisiert",
        "nw" => $altres,
         'breadcrumbs' => [
            'GitDumper - Sync Databases' => route('api.showgit'),
        ],
        'lastUpate'=>date("d.m.Y H:i:s",strtotime($lupd)),
    ],
    ]);
  }
};
