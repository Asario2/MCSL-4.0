<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;


class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {SD}';
    protected $description = 'Generate sitemap.xml from all public GET routes and dynamic picture subpages (filtered by subdomain middleware)';

    public function handle()
    {
        $this->info('Sitemap wird erstellt …');

        $currentSD = $this->argument('SD');
        $conn = Settings::$mariaDBs[$currentSD];

        $globalRoutes = [
            'home/privacy',
        ];


        // === 1️⃣ Statische Laravel-Routen erfassen ===
        $routes = collect(Route::getRoutes())
           ->filter(function ($route) use ($currentSD, $globalRoutes) {
            if (!in_array('GET', $route->methods()) || str_contains($route->uri(), '{')) {
                return false;
            }

            if (str_starts_with($route->uri(), 'api/')) {
                return false;
            }

            // ❌ Admin / Auth etc. raus
            $middlewares = $route->gatherMiddleware();
            $excluded = ['is_admin', 'auth', 'verified'];
            foreach ($excluded as $mw) {
                if (in_array($mw, $middlewares)) {
                    return false;
                }
            }

            // ✅ 1. GLOBAL ROUTES explizit erlauben
            if (in_array($route->uri(), $globalRoutes)) {
                return true;
            }

            // ✅ 2. CheckSubd Middleware prüfen
            foreach ($middlewares as $mw) {
                if (is_string($mw) && str_starts_with($mw, \App\Http\Middleware\CheckSubd::class)) {

                    $parts = explode(':', $mw, 2);

                    if (count($parts) === 2) {
                        $allowed = explode(',', $parts[1]);
                        return in_array($currentSD, $allowed);
                    }

                    return false;
                }
            }

            // ❌ Alles andere fliegt raus
            return false;
        })
            ->map(fn($route) => url($route->uri()))
            ->unique()
            ->values();

        if(Schema::connection($conn)->hasTable("image_categories"))
        {
        // === 2️⃣ Dynamische Picture-Seiten ergänzen ===
        $pictures = DB::connection($conn)->table('image_categories')
            ->where("pub","1")
            ->select('name as slug', 'updated_at')
            ->get(); // Erst get() liefert Collection

        $pictureLinks = $pictures->map(function ($p) use ($currentSD) {
            return [
                'loc' => $this->EXTR_LNK(url('/home/show/pictures/' . $p->slug), $currentSD),
                'lastmod' => $p->updated_at ?? now(),
            ];
        });
        }
        if($currentSD == "mfx")
        {
        // === 2️⃣ Dynamische Picture-Seiten ergänzen ===
        $infos = DB::connection($conn)->table('infos')
            ->where("pub","1")
            ->select('id as slug', 'updated_at')
            ->get(); // Erst get() liefert Collection

        $infosLinks = $infos->map(function ($p) use ($currentSD) {
            return [
                'loc' => $this->EXTR_LNK(url('/home/infos/show/' . $p->slug), $currentSD),
                'lastmod' => $p->updated_at ?? now(),
            ];
        });
        }
        if($currentSD == "ab")
        {
        // === 2️⃣ Dynamische Picture-Seiten ergänzen ===
        $blogs = DB::connection($conn)->table('blogs')
            ->where("pub","1")
            ->select('autoslug as slug', 'updated_at')
            ->get(); // Erst get() liefert Collection

        $blogsLinks = $blogs->map(function ($p) use ($currentSD) {
            return [
                'loc' => $this->EXTR_LNK(url('/blogs/show/' . $p->slug), $currentSD),
                'lastmod' => $p->updated_at ?? now(),
            ];
        });
        }
        // === 3️⃣ XML zusammenbauen ===
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        // Statische Routen hinzufügen
        foreach ($routes as $link) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($this->EXTR_LNK($link, $currentSD), ENT_XML1));
            $url->addChild('lastmod', now()->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.5');
        }
        if(Schema::connection($conn)->hasTable("image_categories")){
        // Dynamische Picture-Seiten hinzufügen
        foreach ($pictureLinks as $entry) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild('lastmod', Carbon::parse($entry['lastmod'])->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.7');
        }
        }
        if(Schema::connection($conn)->hasTable("blogs")){
        // Dynamische Picture-Seiten hinzufügen
        foreach ($blogsLinks as $entry) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild('lastmod', Carbon::parse($entry['lastmod'])->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.6');
        }
        }
         if($currentSD == "mfx"){
        // Dynamische Picture-Seiten hinzufügen
        foreach ($infosLinks as $entry) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild('lastmod', Carbon::parse($entry['lastmod'])->toAtomString());
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', '0.7');
        }
        }
        // === 4️⃣ Datei speichern ===
        $path = public_path("sitemap." . $currentSD . "_v2.xml");
        $xml->asXML($path);

        $this->info("✅ Sitemap für Subdomain {$currentSD} erstellt: {$path}");
    }

    function EXTR_LNK($url, $sd)
    {
        return str_replace(["http://localhost/","http://test.mcs/","http://chh.test.mcs","http://mfx.test.mcs","http://dag.test.mcs","http://ab.test.mcs"], "https://" . Settings::$dom[$sd], $url);
    }
}
