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
    protected $description = 'Generate sitemap.xml from all public GET routes and dynamic pages';

    public function handle()
    {
        $this->info('Sitemap wird erstellt …');

        $currentSD = $this->argument('SD');
        $conn = Settings::$mariaDBs[$currentSD];
        $globalRoutes = ['home/privacy'];

        $routes = collect(Route::getRoutes())
            ->filter(function ($route) use ($currentSD, $globalRoutes) {
                if (!in_array('GET', $route->methods()) || str_contains($route->uri(), '{')) {
                    return false;
                }

                if (str_starts_with($route->uri(), 'api/') || $route->uri() == 'home/terms') {
                    return false;
                }

                $middlewares = $route->gatherMiddleware();

                foreach (['is_admin', 'auth', 'verified'] as $mw) {
                    if (in_array($mw, $middlewares)) {
                        return false;
                    }
                }

                if (in_array($route->uri(), $globalRoutes)) {
                    return true;
                }

                foreach ($middlewares as $mw) {
                    if (is_string($mw) && str_starts_with($mw, \App\Http\Middleware\CheckSubd::class)) {
                        $parts = explode(':', $mw, 2);

                        if (count($parts) === 2) {
                            return in_array($currentSD, explode(',', $parts[1]));
                        }

                        return false;
                    }
                }

                return false;
            })
            ->map(fn($route) => url($route->uri()))
            ->unique()
            ->values();

        $pictureLinks = collect();
        $blogsLinks = collect();
        $infosLinks = collect();

        // === PICTURE-SEITEN ===
        if (
            ($currentSD === 'ab' || $currentSD === 'pna') &&
            Schema::connection($conn)->hasTable('image_categories') &&
            Schema::connection($conn)->hasTable('images')
        ) {
            $pictures = DB::connection($conn)
                ->table('image_categories')
                ->where('pub', 1)
                ->select('id', 'name as slug', 'updated_at')
                ->get();

            $perPage = (int)(Settings::$image_pages[$currentSD] ?? 10);

            foreach ($pictures as $p) {

                $slug = $currentSD === 'ab'
                    ? $p->slug
                    : strtolower($p->slug);

                $baseUrl = $currentSD === 'ab'
                    ? url('/home/show/pictures/' . $slug)
                    : url('/' . $slug);

                $baseUrl = $this->EXTR_LNK($baseUrl, $currentSD);

                $imcount = DB::connection($conn)
                    ->table('images')
                    ->where('pub', 1)
                    ->where('image_categories_id', $p->id)
                    ->count();

                $this->info(
                    "Kategorie: {$slug} | ID: {$p->id} | Bilder: {$imcount} | pro Seite: {$perPage}"
                );

                $pictureLinks->push([
                    'loc' => $baseUrl,
                    'lastmod' => $p->updated_at ?? now(),
                ]);

                if ($perPage > 0 && $imcount > $perPage) {

                    $pages = (int)ceil($imcount / $perPage);

                    $this->info("  -> {$pages} Seiten");

                    for ($page = 2; $page <= $pages; $page++) {

                        $pageUrl = $baseUrl . '?page=' . $page;

                        $this->info("  -> {$pageUrl}");

                        $pictureLinks->push([
                            'loc' => $pageUrl,
                            'lastmod' => $p->updated_at ?? now(),
                        ]);
                    }
                }
            }

            $pictureLinks = $pictureLinks
                ->unique('loc')
                ->values();
        }

        // === INFOS ===
        if ($currentSD === 'mfx' && Schema::connection($conn)->hasTable('infos')) {
            $infos = DB::connection($conn)
                ->table('infos')
                ->where('pub', '1')
                ->select('id as slug', 'updated_at')
                ->get();

            $infosLinks = $infos->map(function ($p) use ($currentSD) {
                return [
                    'loc' => $this->EXTR_LNK(
                        url('/home/infos/show/' . $p->slug),
                        $currentSD
                    ),
                    'lastmod' => $p->updated_at ?? now(),
                ];
            });
        }

        // === BLOGS ===
        if ($currentSD === 'ab' && Schema::connection($conn)->hasTable('blogs')) {
            $blogs = DB::connection($conn)
                ->table('blogs')
                ->where('pub', '1')
                ->select('autoslug as slug', 'updated_at')
                ->get();

            $blogsLinks = $blogs->map(function ($p) use ($currentSD) {
                return [
                    'loc' => $this->EXTR_LNK(
                        url('/blogs/show/' . $p->slug),
                        $currentSD
                    ),
                    'lastmod' => $p->updated_at ?? now(),
                ];
            });
        }

        // === XML ===
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?><urlset/>'
        );

        $xml->addAttribute(
            'xmlns',
            'http://www.sitemaps.org/schemas/sitemap/0.9'
        );

        $usedUrls = [];

        foreach ($routes->unique() as $link) {
            $link = $this->EXTR_LNK($link, $currentSD);

            if (in_array($link, $usedUrls)) {
                continue;
            }

            $usedUrls[] = $link;

            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($link, ENT_XML1));
            $url->addChild('lastmod', now()->toAtomString());
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.5');
        }

        foreach ($pictureLinks as $entry) {
            if (in_array($entry['loc'], $usedUrls)) {
                continue;
            }

            $usedUrls[] = $entry['loc'];

            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild(
                'lastmod',
                Carbon::parse($entry['lastmod'])->toAtomString()
            );
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.7');
        }

        foreach ($blogsLinks as $entry) {
            if (in_array($entry['loc'], $usedUrls)) {
                continue;
            }

            $usedUrls[] = $entry['loc'];

            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild(
                'lastmod',
                Carbon::parse($entry['lastmod'])->toAtomString()
            );
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.6');
        }

        foreach ($infosLinks as $entry) {
            if (in_array($entry['loc'], $usedUrls)) {
                continue;
            }

            $usedUrls[] = $entry['loc'];

            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($entry['loc'], ENT_XML1));
            $url->addChild(
                'lastmod',
                Carbon::parse($entry['lastmod'])->toAtomString()
            );
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', '0.7');
        }

        $path = public_path('sitemap.' . $currentSD . '_v2.xml');

        $xml->asXML($path);

        $this->info(
            "✅ Sitemap für Subdomain {$currentSD} erstellt: {$path}"
        );
    }

    private function EXTR_LNK($url, $sd)
    {
        return str_replace(
            [
                'http://localhost/',
                'http://test.mcs/',
                'http://chh.test.mcs',
                'http://mfx.test.mcs',
                'http://dag.test.mcs',
                'http://ab.test.mcs',
                'http://pna.test.mcs'
            ],
            'https://' . Settings::$dom[$sd],
            $url
        );
    }
}