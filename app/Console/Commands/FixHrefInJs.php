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

        $this->info("===== FixHrefInJs =====");

        /*
        |--------------------------------------------------------------------------
        | Inertia Patch
        |--------------------------------------------------------------------------
        */

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

            $content = File::get($filePath);

            if (
                strpos(
                    $content,
                    "if(!href){\n  href = '';\n}"
                ) !== false
            ) {

                $this->info("✔ Bereits gepatcht");

                continue;
            }

            $search =
'const hasHost = urlHasProtocol(href.toString());';

            $replace =
"if(!href){\n  href = '';\n}\nconst hasHost = urlHasProtocol(href.toString());";

            if (strpos($content, $search) === false) {

                $this->warn("Pattern nicht gefunden.");

                continue;
            }

            $content = str_replace(
                $search,
                $replace,
                $content
            );

            File::put($filePath, $content);

            $this->info("✔ Inertia gepatcht.");
        }

        /*
        |--------------------------------------------------------------------------
        | Part 2 beginnt hier...
        |--------------------------------------------------------------------------
        */
                /*
        |--------------------------------------------------------------------------
        | Vue Shared Patch
        |--------------------------------------------------------------------------
        */

        $vueFiles = [

            base_path('node_modules/@vue/shared/dist/shared.cjs.js'),

            base_path('node_modules/@vue/shared/dist/shared.esm-bundler.js'),

        ];

        foreach ($vueFiles as $vueFile) {

            $this->newLine();

            $this->info("Bearbeite:");

            $this->line($vueFile);

            if (!File::exists($vueFile)) {

                $this->warn("Datei existiert nicht.");

                continue;
            }

            $content = File::get($vueFile);

            if (
                strpos(
                    $content,
                    'if (typeof src !== "string" || src == null)'
                ) !== false
            ) {

                $this->info("✔ Bereits gepatcht.");

                continue;
            }

            $search = <<<'JS'
return src.replace(commentStripRE, "");
JS;

            $replace = <<<'JS'
if (typeof src !== "string" || src == null) {
    return "";
}

return src.replace(commentStripRE, "");
JS;

            if (strpos($content, $search) === false) {

                $this->warn("Pattern nicht gefunden.");

                continue;
            }

            $content = str_replace(
                $search,
                $replace,
                $content
            );

            File::put($vueFile, $content);

            $this->info("✔ Vue Shared gepatcht.");
        }

        /*
        |--------------------------------------------------------------------------
        | Part 3 beginnt hier...
        |--------------------------------------------------------------------------
        */
        /*
|--------------------------------------------------------------------------
| Server Renderer Patch entfernen
|--------------------------------------------------------------------------
*/

$rendererFiles = [
    base_path('node_modules/@vue/server-renderer/dist/server-renderer.cjs.js'),
    base_path('node_modules/@vue/server-renderer/dist/server-renderer.esm-bundler.js'),
];

foreach ($rendererFiles as $rendererFile) {

    $this->newLine();
    $this->info("Bearbeite:");
    $this->line($rendererFile);

    if (!File::exists($rendererFile)) {
        $this->warn("Datei existiert nicht.");
        continue;
    }

    $content = File::get($rendererFile);
    $content = str_replace("\r\n", "\n", $content);

    $search = <<<'JS'
	try {
  const t = vnode?.type;

  console.log("================================");
  console.log("VNode type:", t);

  if (typeof t === "object") {
    console.dir(t, { depth: 2 });
  }

  if (typeof t === "symbol") {
    console.log("Symbol:", t.toString());
  }

  if (typeof t === "function") {
    console.log("Function:", t.name);
  }

} catch(e) {
	console.error(e);
}
JS;

    if (strpos($content, $search) === false) {

        $this->info("✔ Kein renderVNode-Debug vorhanden.");

        continue;
    }

    $content = str_replace($search, "", $content);

    File::put($rendererFile, $content);

    $this->info("✔ renderVNode-Debug entfernt.");
        }
        $this->newLine();

        return self::SUCCESS;
    }

}
