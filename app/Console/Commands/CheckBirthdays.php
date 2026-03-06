<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckBirthdays extends Command
{
    protected $signature = 'birthday:check';
    protected $description = 'Prüft, ob heute jemand Geburtstag oder Todestag hat und sendet eine E-Mail-Benachrichtigung.';

    public function handle()
    {
        $heute = date('d.m');        // Tag.Monat für Vergleich
        $currentYear = date('Y');    // aktuelles Jahr
        $this->info("Starte Geburtstags-/Todestagsprüfung für {$heute}");

        $users = DB::table('asarios_BLog.contacts')
            ->where("us_poster","1")
            ->select('id', 'Vorname', 'Nachname', 'Email', 'Telefon', 'Handy', 'Geburtsdatum', 'ripdate', 'hasyear', 'hasryear')
            ->get();
        $uid = 1;
        foreach ($users as $user) {
            try {
                // 🔹 Entschlüsseln
                $vorname    = $user->Vorname ? decval_user($user->Vorname, $uid) : '';
                $nachname   = $user->Nachname ? decval_user($user->Nachname, $uid) : '';
                $email      = $user->Email ? decval_user($user->Email, $uid) : '';
                $telefon    = $user->Telefon ? decval_user($user->Telefon, $uid) : '';
                $handy      = $user->Handy ? decval_user($user->Handy, $uid) : '';
                $geburtstag = $user->Geburtsdatum ? decval_user($user->Geburtsdatum, $uid) : null;
                $ripdate    = $user->ripdate ? decval_user($user->ripdate, $uid) : null;
                $hasyear    = $user->hasyear ? decval_user($user->hasyear, $uid) : 0;
                $hasryear   = $user->hasryear ? decval_user($user->hasryear, $uid) : 0;
                $id         = $user->id;

                if (!$geburtstag) continue;

                // ====================
                // 🔹 GEBURTSTAG
                // ====================
                if ($hasyear < $currentYear) {
                    if (!preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $geburtstag, $matches)) continue;

                    $tag   = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $monat = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                    $jahr  = $matches[3];
                    $geburtstag_formatiert = "{$tag}.{$monat}";

                    if ($geburtstag_formatiert === $heute) {
                        $birthdayDate = Carbon::createFromFormat('d.m.Y', $geburtstag);
                        $age = $birthdayDate->age;

                        $this->info("🎉 {$vorname} {$nachname} hat heute Geburtstag! ({$age} Jahre)");
                        DB::table("asarios_BLog.contacts")->where("id",$id)->update(["hasyear"=>encval_user(date("Y"),$uid)]);
                        $grad = MCSL_GRAD(); // Header-HTML

                        $text = <<<EOT
<html>
<body>
{$grad}
<br>
{$vorname} {$nachname} hat heute Geburtstag! 🎂<br /><br />
Alter: {$age} Jahre<br />
Tel: {$telefon}<br />
Mobil: {$handy}<br />
Email: {$email}<br />
</body>
</html>
EOT;

                        Mail::html($text, function ($message) use ($vorname, $nachname) {
                            $message->to('[EMAIL]')
                                    ->subject("🎉 Geburtstag: {$vorname} {$nachname}");
                        });

                        // Jahr aktualisieren, um Doppel-Mails zu vermeiden
                        // DB::table('asarios_BLog.contacts')->where('id', $id)->update(['hasyear' => encval($currentYear)]);
                    }
                }

                // ====================
                // 🔹 TODESTAG
                // ====================
                if ($ripdate && $hasryear < $currentYear) {
                    if (!preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $ripdate, $matches)) continue;

                    $tag   = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $monat = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                    $jahr  = $matches[3];
                    $ripdate_formatiert = "{$tag}.{$monat}";

                    if ($ripdate_formatiert === $heute) {
                        $birthdayDate = Carbon::createFromFormat('d.m.Y', $geburtstag);
                        $ripDate      = Carbon::createFromFormat('d.m.Y', $ripdate);
                        $age          = $birthdayDate->diffInYears($ripDate);

                        $this->info("🕯 {$vorname} {$nachname} hat heute Todestag! ({$age} Jahre)");

                        $grad = MCSL_GRAD(); // Header-HTML

                        $text = <<<EOT
<html>
<body>
{$grad}
<br>
{$vorname} {$nachname} hat heute Todestag!<br /><br />
Alter beim Todestag: {$age} Jahre<br />
Verstorben am: {$ripdate}<br />
Tel: {$telefon}<br />
Mobil: {$handy}<br />
Email: {$email}<br />
</body>
</html>
EOT;

                        Mail::html($text, function ($message) use ($vorname, $nachname) {
                            $message->to('[EMAIL]')
                                    ->subject("Todestag: {$vorname} {$nachname}");
                        });

                        // Jahr aktualisieren, um Doppel-Mails zu vermeiden
                        // DB::table('asarios_BLog.contacts')->where('id', $id)->update(['hasryear' => encval($currentYear)]);
                    }
                }

            } catch (\Exception $e) {
                $this->error("Fehler bei Benutzer {$user->id}: " . $e->getMessage());
            }
        }

        $this->info('Geburtstags-/Todestagsprüfung abgeschlossen.');
    }
}
