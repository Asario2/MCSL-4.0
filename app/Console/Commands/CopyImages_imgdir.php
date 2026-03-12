<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyImages_imgdir extends Command
{
    protected $signature = 'images:copy-imgdirs';
    protected $description = 'Sucht in E:\\ nach Bilddateien, die in imgdir_content referenziert sind und kopiert sie nach /orig';

    protected $directories = [
        'E:\\phbgup',
    ];

    protected $ic = 0;
    protected $oc = 0;

    public function handle()
    {
        $this->info("🔍 Scanne imgdir_content/... nach vorhandenen Bildern...");

        $imageList = [];
        $baseDir = public_path("images/_mfx/images/imgdir_content");

        // Ordner sammeln
        $folders = File::directories($baseDir);

        // Zusätzliche (manuelle) Ordner ergänzen – aber nur, wenn sie existieren
        $extraFolders = ['asd', 'hallo_222', 'ki_23', 'makro_2', 'makro_3', 'serengeti'];
        foreach ($extraFolders as $extra) {
            $path = realpath($baseDir . DIRECTORY_SEPARATOR . $extra);
            if ($path && !in_array($path, $folders)) {
         //       $folders[] = $path;
            }
        }

        // Verarbeite alle Ordner und baue $imageList
        foreach ($folders as $folder) {
            $folderName = basename($folder);
            $targetOrig = $folder . DIRECTORY_SEPARATOR . "orig";

            $this->line("🔍 Bearbeite Ordner: $folder");

            if (!File::exists($targetOrig)) {
                File::makeDirectory($targetOrig, 0755, true);
                $this->info("📁 Zielordner erstellt: $targetOrig");
            }

            $files = File::files($folder);
            $this->line("📂 Dateien im Ordner $folder:");
            foreach ($files as $file) {
                $this->line(" - " . $file->getFilename());

                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $fileName = ($file->getFilename());
                    $imageList[$fileName] = $targetOrig;
                    // $this->line("📝 Hinzugefügt: $fileName → $targetOrig");
                }
            }
        }

        $this->info("📦 Gesammelte Bildnamen: " . count($imageList));
//         \Log::info('imageList', $imageList);

        // Suche in Quelldateisystem und kopiere gefundene Bilder
        foreach ($this->directories as $dir) {
            $this->searchAndCopy($dir, $imageList);
        }

        // Ausgabe aller erwarteten Bilder zur Kontrolle
        foreach ($imageList as $file => $target) {
            $this->line("💡 Erwartet: $file → $target");
        }
        $this->info("📂 Alle verarbeiteten Ordner:");
            foreach ($folders as $folder) {
                $this->line(" - $folder");
            }
        $this->info("✅ Fertig! ($this->ic Dateien kopiert oder ersetzt)");
        $this->info("✅ Fertig! ($this->oc Dateien übersprungen)");
    }

    protected function searchAndCopy($dir, $imageList)
    {
        if (!File::exists($dir)) {
            $this->warn("⚠ Verzeichnis nicht gefunden: $dir");
            return;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        foreach (File::allFiles($dir) as $file) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExtensions)) continue;

            $fileName = ($file->getFilename());
            $sourcePath = $file->getRealPath();

            if (!isset($imageList[$fileName])) {
                continue; // Nicht referenziert
            }

            $destination = $imageList[$fileName] . DIRECTORY_SEPARATOR . $fileName;

            if (!File::exists($destination)) {
                File::copy($sourcePath, $destination);
                $this->ic++;
                $this->info("🆕 Kopiert: $fileName → $destination");
            } else {
                $srcSize = filesize($sourcePath);
                $dstSize = filesize($destination);
                $this->oc++;
                // if ($srcSize > $dstSize || !is_file($destination)) {
                    File::copy($sourcePath, $destination);
                    $this->ic++;
                    $this->info("🔁 Ersetzt (größer): $fileName");
                // }
            }
        }
    }
}
