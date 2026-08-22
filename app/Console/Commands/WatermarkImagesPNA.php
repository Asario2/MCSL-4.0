<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class WatermarkImagesPNA extends Command
{
    protected $signature = 'images:watermark {path}';

    protected $description = 'Skaliert Bilder auf 800px und setzt das Copyright unten rechts';

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

        $files = glob($orig . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);

        if (!$files) {
            $this->warn('Keine Bilder gefunden.');
            return self::SUCCESS;
        }

        foreach ($files as $file) {

            try {
                $image = $manager->read($file);

                // Auf 800px Breite skalieren, Höhe proportional
                $image->scale(width: 1400);

                // Copyright laden
                $logo = $manager->read($logoPath);

                // Logo maximal 25% der Bildbreite
                $logo->scaleDown(width: (int) ($image->width() * 0.25));

                // Unten rechts mit 20px Abstand
                $image->place(
                    $logo,
                    'bottom-right',
                    20,
                    20
                );

                $filename = basename($file);
                $target = $path . '/big/' . $filename;

                $image->save($target);

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
