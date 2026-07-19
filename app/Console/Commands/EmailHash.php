<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmailHash extends Command
{
    /**
     * Der Konsolenbefehl (Artisan name)
     */
    protected $signature = 'email:hash';

    /**
     * Beschreibung (wird in `php artisan list` angezeigt)
     */
    protected $description = 'Erstellt Hashes für alle E-Mail-Adressen in der contacts-Tabelle';

    public function handle()
    {
        $contacts = DB::connection("mariadb")->table('contacts')->get();
        //$this->info("Verarbeite {$contacts->count()} Kontakte...");

        foreach ($contacts as $contact) {

            \Log::info([
                'id' => $contact->id,
                'email' => $contact->Email,
            ]);

            if (empty($contact->Email)) {
                continue;
            }

            try {
                $hash = hash('sha256', trim(decval_user($contact->Email,$contact->us_poster)));
                \Log::info("hash: ".$hash." | ".decval_user($contact->Email,$contact->us_poster));
                DB::connection("mariadb")->table('contacts')
                    ->where('id', $contact->id)
                    ->update(['email_hash' => $hash]);

          //      $this->line("✅ {$contact->Email} → {$hash}");
            } catch (\Exception $e) {
                $this->error("Fehler bei ID {$contact->id}: " . $e->getMessage());
            }
        }

        //$this->info('Alle E-Mail-Hashes wurden erfolgreich generiert.');
        return Command::SUCCESS;
    }
}
