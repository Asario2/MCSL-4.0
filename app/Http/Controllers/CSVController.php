<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


class CSVController extends Controller
{
    public function importContacts(Request $request)
{
    $uploadedFile = $request->file('csv');

    if (!$uploadedFile) {
        return response()->json(['error' => 'Keine Datei hochgeladen'], 400);
    }

    $contacts = [];

    // CSV-Inhalt laden und ";" durch "," ersetzen
    $content = str_replace(";", ",", file_get_contents($uploadedFile->getRealPath()));

    // Memory-Stream öffnen
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $content);
    rewind($handle);

    if ($handle !== false) {
        $header = null;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $row = array_map(function ($v) {
                return mb_convert_encoding($v, 'UTF-8', mb_detect_encoding($v, 'UTF-8, ISO-8859-1, Windows-1252', true));
            }, $row);

            if (!$header) {
                $header = $row;
                continue;
            }

            $row = array_combine($header, $row);

            $phoneNumbers = [];
            $mobileNumbers = [];

            $firstName = '';
            $lastName = '';
            $fullName = '';
            $email = '';

            if (in_array('E-mail 1 - Value', $header)) {
                // Google/Android CSV
                $firstName = $row['First Name'] ?? '';
                $lastName = $row['Last Name'] ?? '';
                $fullName = trim($firstName . ' ' . $lastName);
                $email = $row['E-mail 1 - Value'] ?? '';

                for ($i = 1; $i <= 2; $i++) {
                    $phone = trim($row["Phone {$i} - Value"] ?? '');
                    $label = strtolower($row["Phone {$i} - Label"] ?? '');
                    if (!$phone) continue;

                    $phone = preg_replace('/^\+49\s?/', '0', $phone);

                    if ($label === 'mobile' || $this->isGermanMobile($phone)) {
                        $mobileNumbers[] = $phone;
                    } else {
                        $phoneNumbers[] = $phone;
                    }
                }
            } else {
                // iOS CSV
                $fullName = trim($row['Kompletter Name'] ?? '');
                $firstName = trim($row['Vorname'] ?? '');
                $lastName = trim($row['Nachname'] ?? '');
                $email = '';

                $phonesRaw = [$row['Telefon - Telefon'] ?? ''];
                $mobilesRaw = [$row['Telefon - Mobil'] ?? ''];

                foreach ($phonesRaw as $p) {
                    $p = trim($p);
                    if ($p === '') continue;
                    $normalized = preg_replace('/[^0-9]/', '', $p);
                    if ($this->isGermanMobile($normalized)) {
                        $mobileNumbers[] = $p;
                    } else {
                        $phoneNumbers[] = $p;
                    }
                }

                foreach ($mobilesRaw as $m) {
                    $m = trim($m);
                    if ($m === '') continue;
                    $normalized = preg_replace('/[^0-9]/', '', $m);
                    if ($this->isGermanMobile($normalized)) {
                        $mobileNumbers[] = $m;
                    } else {
                        $phoneNumbers[] = $m;
                    }
                }
            }

            // ✅ Kontakt hinzufügen (für Android & iOS)
            $contacts[] = [
                'full_name'  => $fullName,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'phones'     => $phoneNumbers,
                'mobiles'    => $mobileNumbers,
                'selected'   => true,
            ];
        }

        fclose($handle);
    }

    return response()->json(['contacts' => $contacts]);
}

function isGermanMobile(string $phone): bool
{
    // Leerzeichen, Bindestriche, Klammern entfernen
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

    // +49 durch 0 ersetzen
    if (str_starts_with($phone, '+49')) {
        $phone = '0' . substr($phone, 3);
    }

    // Nur Ziffern prüfen
    if (!preg_match('/^\d+$/', $phone)) {
        return false;
    }

    // Mobilnummern beginnen mit 015, 016, 017 (nicht 0180)
    if (preg_match('/^01[567]\d{7,}$/', $phone)) {
        return true;
    }

    return false;
}


    public function importContacts_old(Request $request, $file='')
    {
        // Datei vom Request
        $uploadedFile = $request->file('csv');

        if (!$uploadedFile) {
            return response()->json(['error' => 'Keine Datei hochgeladen'], 400);
        }

        // Zielpfad auf Server
        $path = public_path("/files/_ab/kontakte/" . md5($uploadedFile->getClientOriginalName() . "_" . Auth::id()) . "." . $uploadedFile->getClientOriginalExtension());
        // \Log::info("PATH:" . $path);

        if (!file_exists($path)) {
            return response()->json(['error' => 'Datei existiert nicht auf dem Server'], 404);
        }

        $contacts = [];
        $handle = fopen($path, 'r');
        if ($handle !== false) {
            $header = null;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                // Alle Felder in UTF-8 konvertieren
                $row = array_map(function ($v) {
                    return mb_convert_encoding($v, 'UTF-8', 'auto');
                }, $row);

                // Erste Zeile = Header
                if (!$header) {
                    $header = $row;
                    continue;
                }

                $row = array_combine($header, $row);

            if(in_array('E-mail 1 - Value',$header))
            {
                // Name
                $firstName = $row['First Name'] ?? '';
                $lastName = $row['Last Name'] ?? '';
                $fullName = trim($firstName . ' ' . $lastName);

                // Email
                $email = $row['E-mail 1 - Value'] ?? '';

                // Telefonnummern
                $phoneNumbers = [];
                $mobileNumbers = [];

                for ($i = 1; $i <= 2; $i++) {
                    $phone = $row["Phone {$i} - Value"] ?? '';
                    $label = strtolower($row["Phone {$i} - Type"] ?? '');

                    if (!$phone) continue;

                    // +49 auf 0 ersetzen
                    $phone = preg_replace('/^\+49\s?/', '0', $phone);

                    if ($label == 'mobile') {
                        $mobileNumbers[] = $phone;
                    } else {
                        $phoneNumbers[] = $phone;
                    }
                }
            }


                $contacts[] = [
                    'full_name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phones' => $phoneNumbers,
                    'mobiles' => $mobileNumbers,
                ];
            }

            fclose($handle);
        }

        $this->DUMPTEMP();
        // JSON an Vue zurückgeben
        return response()->json([
            'contacts' => $contacts
        ]);
    }

public function saveImportedContacts(Request $request)
{

    $contacts = $request->input('contacts', []);
    // \Log::info("Anzahl CSV Kontakte: " . count($contacts));

    $dup = 0;
    $new = 0;
    $now = now();
    $id = 0;

    foreach ($contacts as $c) {

        $id++;
        // \Log::info("--------------------------------------------------");
        // \Log::info("Kontakt #{$id}");
        // \Log::info("RAW CSV Kontakt:", $c);

        // --------------------------------------------------
        // 1️⃣ Mapping: normalisiert → original
        // --------------------------------------------------
        $numberMap = [];
        $allNumbers = [];

        $mergedNumbers = array_merge($c['phones'] ?? [], $c['mobiles'] ?? []);
        // \Log::info("Zusammengeführte CSV Nummern:", $mergedNumbers);

        foreach ($mergedNumbers as $p) {

            // \Log::info("Original Feldwert: " . $p);

            $parts = preg_split('/\s*(:::|\|\|\|)\s*/', $p);
            // \Log::info("Gesplittete Teile:", $parts);

            foreach ($parts as $nr) {

                $norm = $this->normalizePhone($nr);

                // \Log::info("Nummer vor Normalisierung: {$nr}");
                // \Log::info("Nummer nach Normalisierung: " . ($norm ?? 'NULL'));

                if (!$norm) continue;

                $numberMap[$norm] = $nr;
                $allNumbers[] = $norm;
            }
        }

        $allNumbers = array_unique($allNumbers);

        // \Log::info("Finale normalisierte CSV Nummern:", $allNumbers);
        // \Log::info("NumberMap:", $numberMap);

        if (empty($allNumbers)) {
            \Log::info("Keine gültigen Nummern → Überspringe Kontakt");
            continue;
        }

        // --------------------------------------------------
        // 2️⃣ Bestehenden Kontakt suchen
        // --------------------------------------------------
        $matchNumber = null;

        // \Log::info("Starte DB Vergleich...");

        $existingContact = DB::table('contacts')->get()->first(function ($row) use ($allNumbers, &$matchNumber, $numberMap) {

            $dbNumbers = [];

            // \Log::info("Prüfe DB Kontakt ID {$row->id} Name {$row->Name}");

            foreach (['Telefon', 'Handy'] as $field) {

                if (!$row->$field) continue;

                $rawValue = decval_user($row->$field,Auth::id());
                // \Log::info("DB Feld {$field} RAW: " . $rawValue);

                $parts = preg_split('/\s*(:::|\|\|\|)\s*/', $rawValue);

                foreach ($parts as $part) {

                    $norm = $this->normalizePhone($part);

                    // \Log::info("DB Teilnummer: {$part}");
                    // \Log::info("DB normalisiert: " . ($norm ?? 'NULL'));

                    if ($norm) $dbNumbers[] = $norm;
                }
            }

            // \Log::info("DB normalisierte Nummern:", $dbNumbers);

            $intersect = array_intersect($allNumbers, $dbNumbers);

            // \Log::info("Intersection:", $intersect);

            if (!empty($intersect)) {

                $norm2 = reset($intersect);
                $matchNumber = $numberMap[$norm2] ?? null;

                // \Log::info("MATCH GEFUNDEN mit Nummer: {$norm2}");
                // \Log::info("Originale Match Nummer aus CSV: " . ($matchNumber ?? 'NULL'));

                return true;
            }

            return false;
        });

        // --------------------------------------------------
        // 3️⃣ UPDATE
        // --------------------------------------------------
        if ($existingContact) {

            \Log::info("Bestehender Kontakt gefunden: ".decval_user($existingContact->Name,Auth::id()));

            $existingNumbers = [];

            foreach (['Telefon', 'Handy'] as $field) {

                $value = $existingContact->$field ? decval_user($existingContact->$field,Auth::id()) : '';

                if (empty($value)) continue;

                // \Log::info("Bestehendes Feld {$field}: {$value}");

                $parts = preg_split('/\s*(:::|\|\|\|)\s*/', $value);

                foreach ($parts as $part) {

                    $norm = $this->normalizePhone($part);

                    // \Log::info("Bestehende Teilnummer: {$part}");
                    // \Log::info("Normalisiert: " . ($norm ?? 'NULL'));

                    if ($norm) $existingNumbers[] = $norm;
                }
            }

            // \Log::info("Bestehende normalisierte Nummern:", $existingNumbers);

            $missing = array_diff($allNumbers, $existingNumbers);
            // \Log::info("Fehlende Nummern:", $missing);

            $addedOriginals = [];
            $updateData = [];

            foreach ($missing as $nr) {

                if (!isset($numberMap[$nr])) continue;

                $original = $numberMap[$nr];
                $addedOriginals[] = $original;

                // \Log::info("Füge fehlende Nummer hinzu: {$original}");

                if (str_starts_with($nr, '01')) {

                    $current = $existingContact->Handy ? decval_user($existingContact->Handy,Auth::id()) : '';
                    $newValue = $current ? $current . ' ::: ' . $original : $original;

                    $updateData['Handy'] = encval_user($newValue,Auth::id());

                } else {

                    $current = $existingContact->Telefon ? decval_user($existingContact->Telefon,Auth::id()) : '';
                    $newValue = $current ? $current . ' ::: ' . $original : $original;

                    $updateData['Telefon'] = encval_user($newValue,Auth::id());
                }
            }

            // \Log::info("UpdateData:", $updateData);

            if (!empty($updateData)) {
                DB::table('contacts')->where('id', $existingContact->id)->update($updateData);
                // \Log::info("UPDATE vorbereitet für ID {$existingContact->id}");
            }

            if (!empty($addedOriginals)) {
                // \Log::info("UPDATED: {$existingContact->Name} | Hinzugefügt: " . implode(', ', $addedOriginals));
            } else {
                // \Log::info("DUPLIKAT (keine Änderung): ".decval_user($existingContact->Name,Auth::id())." Matched Number {$matchNumber}");
            }

            $dup++;
            continue;
        }

        // --------------------------------------------------
        // 4️⃣ INSERT
        // --------------------------------------------------
        // \Log::info("Kein bestehender Kontakt gefunden → INSERT");

        $phones = [];
        $mobiles = [];

        foreach ($allNumbers as $nr) {

            if (!isset($numberMap[$nr])) continue;

            if (str_starts_with($nr, '01')) {
                $mobiles[] = $numberMap[$nr];
            } else {
                $phones[] = $numberMap[$nr];
            }
        }

        // \Log::info("Insert Telefon:", $phones);
        // \Log::info("Insert Handy:", $mobiles);

        $co = [
            'pub'        => "1",
            "Gruppe"     => "Friends",
            'Name'       => $c['full_name'] ? encval_user($c['full_name'], Auth::id()) : '',
            'Vorname'    => $c['first_name'] ? encval_user($c['first_name'], Auth::id()) : '',
            'Nachname'   => $c['last_name'] ? encval_user($c['last_name'], Auth::id()) : '',
            'Email'      => $c['email'] ? encval_user($c['email'], Auth::id()) : null,
            'Telefon'    => !empty($phones)  ? encval_user(implode(' ::: ', $phones),Auth::id())  : null,
            'Handy'      => !empty($mobiles) ? encval_user(implode(' ::: ', $mobiles),Auth::id()) : null,
            "us_poster" => Auth::id(),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // \Log::info("Insert Daten:", $co);

        DB::table('contacts')->insert($co);

        // \Log::info("NEU: {$c['full_name']}");
        $new++;
    }

    // \Log::info("===== ENDE saveImportedContacts =====");
    // \Log::info("Neue Datensätze: $new ||| Vorhandene Datensätze: $dup ||| Letzte ID: $id");

    return response()->json([
        'type' => 'success',
        'message' => 'Kontakte gespeichert'
    ]);
}
    private function normalizePhone($phone)
    {
        if (!$phone) return null;
        $orip = $phone;
        // alles außer Zahlen entfernen
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 0049 → 0
        if (str_starts_with($phone, '0049')) {
            $phone = '0' . substr($phone, 4);
        }

        // 49 → 0
        if (str_starts_with($phone, '49')) {
            $phone = '0' . substr($phone, 2);
        }
        if(str_starts_with($orip,"+49 "))
        {
            $phone = "0".substr($orip,-4);
        }
        if(Substr_count($orip,"+") && !substr_count($orip,"+49"))
        {
        $phone = '+'.$phone;
        }

        return $phone;
    }

}

