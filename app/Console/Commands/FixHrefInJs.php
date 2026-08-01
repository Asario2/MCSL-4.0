<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixHrefInJs extends Command
{
    protected $signature = 'fix:href-js';
    protected $description = 'Fix JS files with hardcoded absolute paths + Vue SSR fix';

    public function handle(): int
    {



    // $this->info("===== START =====");

        $files = [
            base_path('node_modules/@inertiajs/core/dist/index.esm.js'),
            base_path('node_modules/@inertiajs/core/dist/index.js'),
            base_path('node_modules/.vite/deps/@inertiajs_vue3.js'),
        ];

        foreach ($files as $filePath) {

            $this->newLine();
            $this->info("Bearbeite:");
            $this->line($filePath);

            if (!File::exists($filePath)) {
                $this->warn("Datei existiert nicht.");
                continue;
            }

            $this->info("✔ exists");

            $content = File::get($filePath);

            $this->info("✔ gelesen");

            if (strpos($content, "if(!href){\n  href = '';\n}") !== false) {
                $this->info("✔ bereits gepatcht");
                continue;
            }

            if (strpos($content, "const hasHost = urlHasProtocol(href.toString());") !== false) {

                $this->info("✔ Pattern gefunden");

                $content = str_replace(
                    "const hasHost = urlHasProtocol(href.toString());",
                    "if(!href){\n  href = '';\n}\nconst hasHost = urlHasProtocol(href.toString());",
                    $content
                );

                $this->info("→ schreibe Datei");

                File::put($filePath, $content);

                $this->info("✔ geschrieben");
            } else {
                $this->warn("Pattern nicht gefunden");
            }
        }

        $this->newLine();

        $vueFile = base_path('node_modules/@vue/shared/dist/shared.cjs.js');

        $this->line($vueFile);

        if (!File::exists($vueFile)) {

            $this->warn("JS-Datei existiert nicht.");

        } else {

            $this->info("✔ exists");

            $content = File::get($vueFile);

            $this->info("✔ gelesen");

            if (strpos($content, 'if(typeof src !== "string")') !== false) {

                $this->info("✔ bereits gepatcht");

            } else {

                $search = 'return src.replace(commentStripRE, "");';

                if (strpos($content, $search) !== false) {

                    $this->info("✔ Pattern gefunden");

$replace = <<<'JS'
if (typeof src !== "string") {
  console.error("================================");
  console.error("TYPE:", typeof src);
  console.dir(src, { depth: 10 });

  if (src && typeof src === "object") {
    console.error("keys:", Object.keys(src));
    console.dir(src, { depth: 10 });
  }

//   console.trace("escapeHtmlComment");

//   throw new Error(
//     "escapeHtmlComment received: " +
//     JSON.stringify(src)
//   );
}

return src.replace(commentStripRE, "");
JS;

                    $content = str_replace($search, $replace, $content);

                    $this->info("→ schreibe JS-Datei");

                    File::put($vueFile, $content);

                    $this->info("✔ JS-Datei geschrieben");

                } else {

                    $this->warn("Pattern nicht gefunden.");
                }
            }
        }

        $this->newLine();


        return self::SUCCESS;
    }
}
