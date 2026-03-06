<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB as DBFacade;

class EncryptEmailsInTable extends Command
{
    protected $signature = 'email:encrypt-all
        {--DB= : Name der Datenbank}
        {--TB= : Tabellenname}
        {--COL= : Spaltenname}
        {--dry : Nur anzeigen, nichts speichern}';

    protected $description = 'Verschlüsselt alle E-Mail-Adressen einer Tabelle mit encval()';

    public function handle(): int
    {
        $database = $this->option('DB');
        $table = $this->option('TB');
        $column = $this->option('COL');
        $dryRun = $this->option('dry');

        if (!$database || !$table || !$column) {
            $this->error('❌ Bitte --DB, --TB und --COL angeben');
            return Command::FAILURE;
        }

        if (!function_exists('encval')) {
            $this->error('❌ encval() Funktion existiert nicht');
            return Command::FAILURE;
        }

        // Dynamische Verbindung zur Datenbank
        $conn = DBFacade::connection();
        $conn->statement("USE {$database}");

        // Datensätze holen
        $rows = $conn->table($table)->select('id', $column)->get();

        if ($rows->isEmpty()) {
            $this->warn('⚠️ Keine Datensätze gefunden');
            return Command::SUCCESS;
        }

        $this->info("🔄 Bearbeite {$rows->count()} Datensätze aus {$database}.{$table}.{$column}");

        $updated = 0;

        foreach ($rows as $row) {
            $email = $row->{$column};

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $encrypted = encval(decval(decval_user($email,Auth::id())));

            if ($dryRun) {
                $this->line("DRY: {$email} → {$encrypted}");
                continue;
            }

            $conn->table($table)
                ->where('id', $row->id)
                ->update([
                    $column => $encrypted,

                ]);

            $updated++;
        }

        if ($dryRun) {
            $this->warn('🧪 DRY-RUN beendet – keine Daten wurden gespeichert');
        } else {
            $this->info("✅ {$updated} E-Mail(s) erfolgreich verschlüsselt");
        }

        return Command::SUCCESS;
    }
}
