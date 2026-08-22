<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class WatermarkImagesPNA extends Command
{
    protected $signature = 'images:watermark {path}';

    protected $description = 'Skaliert Bilder auf 800px und 1400px und setzt das Copyright';

    public function handle()
    {
        $path = rtrim($this->argument('path'), '/\\');

        $orig = $path . '/orig';

        $logoPath = public_path('images/copyleft/paulnadler.png');

        if (!is_dir($orig)) {
            $this->error("Orig-Verzeichnis nicht gefunden: {$orig}");
            return self::FAILURE;
        }

        if (!file_exists($logoPath)) {
            $this->error("Copyright-Grafik nicht gefunden: {$logoPath}");
            return self::FAILURE;
        }

        $manager = new ImageManager(new Driver());

        $files = glob(
            $orig . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}',
            GLOB_BRACE
        );

        if (!$files) {
            $this->warn('Keine Bilder gefunden.');
            return self::SUCCESS;
        }

        // big-Verzeichnis anlegen
        if (!is_dir($path . '/big')) {
            mkdir($path . '/big', 0755, true);
        }

        foreach ($files as $file) {

            try {

                $filename = basename($file);


                /*
                 * =====================================================
                 * 1. HAUPTORDNER
                 * 800px + COPYRIGHT
                 * =====================================================
                 */

                $image800 = $manager->read($file);

                // Auf 800px Breite skalieren
                $image800->scale(width: 800);

                // Copyright laden
                $logo800 = $manager->read($logoPath);

                // Logo maximal 25% der Bildbreite
                $logo800->scaleDown(
                    width: (int) ($image800->width() * 0.25)
                );

                // Copyright unten rechts
                $image800->place(
                    $logo800,
                    'bottom-right',
                    20,
                    20
                );

                // Im Hauptordner speichern
                $target2 = $path . '/' . $filename;

                $image800->save($target2);


                /*
                 * =====================================================
                 * 2. BIG-ORDNER
                 * 1400px + COPYRIGHT
                 * =====================================================
                 */

                $image1400 = $manager->read($file);

                // Auf 1400px Breite skalieren
                $image1400->scale(width: 1400);

                // Copyright laden
                $logo1400 = $manager->read($logoPath);

                // Logo maximal 25% der Bildbreite
                $logo1400->scaleDown(
                    width: (int) ($image1400->width() * 0.25)
                );

                // Copyright unten rechts
                $image1400->place(
                    $logo1400,
                    'bottom-right',
                    20,
                    20
                );

                // In big speichern
                $target = $path . '/big/' . $filename;

                $image1400->save($target);


                $this->info("OK: {$filename}");

            } catch (\Throwable $e) {

                $this->error(
                    "FEHLER {$file}: " . $e->getMessage()
                );
            }
        }

        $this->info('Fertig.');

        return self::SUCCESS;
    }
}
