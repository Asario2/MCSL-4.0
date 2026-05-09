<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixHrefInJs extends Command
{
    protected $signature = 'fix:href-js';
    protected $description = 'Fix JS files with hardcoded absolute paths + Vue SSR fix';

    public function handle()
    {
        $files = [
            base_path('node_modules/@inertiajs/core/dist/index.esm.js'),
            base_path('node_modules/@inertiajs/core/dist/index.js'),
            base_path('node_modules/.vite/deps/@inertiajs_vue3.js'),
        ];

        foreach ($files as $filePath) {

            if (!File::exists($filePath)) {
                $this->warn("File does not exist: $filePath");
                continue;
            }

            $content = File::get($filePath);

            if (strpos($content, "if(!href){\n  href = '';\n}") !== false) {
                $this->info("Already fixed: $filePath");
                continue;
            }

            if (strpos($content, "const hasHost = urlHasProtocol(href.toString());") !== false) {

                $content = str_replace(
                    "const hasHost = urlHasProtocol(href.toString());",
                    "if(!href){\n  href = '';\n}\nconst hasHost = urlHasProtocol(href.toString());",
                    $content
                );

                File::put($filePath, $content);

                $this->info("Fixed: $filePath");
            } else {
                $this->warn("No match in: $filePath");
            }
        }

        // 🔥 NEUER TEIL: Vue shared fix
        $vueFile = base_path('node_modules/@vue/shared/dist/shared.cjs.js');

        if (!File::exists($vueFile)) {
            $this->warn("Vue shared file not found: $vueFile");
        } else {
            $content = File::get($vueFile);

            // Prüfen ob Fix schon drin ist
            if (strpos($content, 'if(typeof src !== "string")') !== false) {
                $this->info("Vue shared already fixed.");
            } else {

                $search = 'return src.replace(commentStripRE, "");';

                if (strpos($content, $search) !== false) {

                    $replace = <<<JS
if(typeof src !== "string")
{
  src = '';
}
return src.replace(commentStripRE, "");
JS;

                    $content = str_replace($search, $replace, $content);

                    File::put($vueFile, $content);

                    $this->info("$vueFile patched.");
                } else {
                    $this->warn("Pattern not found in Vue shared.");
                }
            }
        }

        $this->info('Done.');
    }
}
