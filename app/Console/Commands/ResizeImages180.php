<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ResizeImages180 extends Command
{
    protected $signature = 'images180:resize {source} {target}';
    protected $description = 'Kopiert Bilder und verkleinert sie auf 180px Höhe';

    public function handle()
    {
        $source = rtrim($this->argument('source'), '/');
        $target = rtrim($this->argument('target'), '/');

        if (!File::exists($source)) {
            $this->error("Quellordner existiert nicht.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($target);

        $extensions = ['jpg','jpeg','png','gif','webp','bmp'];

        foreach (File::allFiles($source) as $file) {

            if (!in_array(strtolower($file->getExtension()), $extensions)) {
                continue;
            }

            $relative = $file->getRelativePath();
            $destDir = $target . ($relative ? '/' . $relative : '');

            File::ensureDirectoryExists($destDir);

            $dest = $destDir . '/' . $file->getFilename();

            Image::read($file->getPathname())
                ->scale(height: 180)
                ->save($dest);

            $this->line($dest);
        }

        $this->info('Fertig.');
        return self::SUCCESS;
    }
}
