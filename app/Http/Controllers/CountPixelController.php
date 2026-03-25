<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Settings;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class CountPixelController extends Controller
{
    protected $excludeRoutes = [
        'admin.*',
        '*statistics*',
        'cookieconsent.*',
        '*stats*',
        'tables.noview',
        'api.showgit',
        'api.*',
    ];

    protected $excludeURLs = [
        '*.php*',
        '*.js*',
        '*.css*',
        '*.png*',
        '*.jpg*',
        '*.jpeg*',
        '*.svg*',
        '*.webp*',
        '*.gif*',
        '*.ico*',
        '%20',
    ];

    public static array $nostats = [
        '/admin',
        '/_debug',
        '/api/',
        'login',
        'pm/index',
        'mail/subscr',
    ];

    public function track(Request $request)
    {
        try {
            $host = $request->getHost();
            if ($host === 'mail.marblefx.net') {
                return $this->pixelResponse();
            }

            $routeName = $request->query('route') ?? 'unknown';

            // 0️⃣ Leere, ungültige oder 404-Routen ausschließen
            if ($routeName === null || $routeName === '' || $routeName === 'unknown' || Route::getRoutes()->getByName($routeName) === null) {
                return $this->pixelResponse();
            }

            // 1️⃣ Route anhand der Muster ausschließen
            foreach ($this->excludeRoutes as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    return $this->pixelResponse();
                }
            }
            // URL bereinigen
            $rawUrl = $this->SH($request->query('url')) ?? '/';

            // 2️⃣ URL anhand Dateiendungen ausschließen
            foreach ($this->excludeURLs as $pattern) {
                if (fnmatch($pattern, $rawUrl)) {
                    return $this->pixelResponse();
                }
            }
            foreach (Settings::$FilterUrls as $pattern) {
                if (fnmatch($pattern, $rawUrl)) {
                    return $this->pixelResponse();
                }
            }

            // 3️⃣ Prüfen ob Route Auth-Middleware besitzt
            $routeAction = Route::getRoutes()->getByName($routeName)?->action ?? [];
            $middlewares = $routeAction['middleware'] ?? [];
            $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

            if (in_array('auth', $middlewares)) {
                return $this->pixelResponse();
            }

            // 4️⃣ IP anonymisieren
            $ip = $request->ip();
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $parts = explode('.', $ip);
                $parts[3] = '0';
                $ip = implode('.', $parts);
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $parts = explode(':', $ip);
                $parts[count($parts) - 1] = '0000';
                $ip = implode(':', $parts);
            }

            // 5️⃣ Tracking speichern
            PageView::create([
                'dom'        => SD(),
                'url'        => $rawUrl,
                'ip'         => $ip,
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("CountPixel DB Error: " . $e->getMessage());
        }

        return $this->pixelResponse();
    }

    public static function o404()
    {
        $path = request()->getPathInfo();
        DB::connection("mariadb")->table("xgen_page_views")
            ->where("url", $path)
            ->where("visited_at", ">", now()->subMinutes(2))
            ->delete();
        DB::connection("mariadb")->statement('ALTER TABLE xgen_page_views AUTO_INCREMENT = 1');
    }

    protected function pixelResponse()
    {
        $pixel = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII='
        );

        return response($pixel, 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Length', strlen($pixel))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function delete_stats(Request $request)
    {
        try {
    $url = ltrim($request->url, "/");
    $url = $this->killtimestamp($url);
    \Log::info("DELETE URL: {$url} | dom: {$request->dom}");

    $dom = $request->dom ?? SD();

    $query = DB::connection('mariadb')->table('xgen_page_views')
        ->where("url", "LIKE", "%{$url}%");

    // Prüfen, ob "all" angefragt und Rechte vorhanden
    if ($dom !== "all" || !CheckZRights("StatisticsAll")) {
        $query->where('dom', $dom);
    }

    $deleted = $query->delete(); // Anzahl der gelöschten Zeilen
    \Log::info("Deleted rows: {$deleted}");

    // Optional: dauerhaft in FilterUrls speichern
    if ($request->save) {
        $file = app_path('Models/Settings.php');
        $urlToAdd = "'" . addslashes($url) . "'";
        $content = file_get_contents($file);

        if (!str_contains($content, $urlToAdd)) {
            $pattern = '/public static array \$FilterUrls\s*=\s*\[(.*?)\];/s';
            $content = preg_replace_callback($pattern, function ($matches) use ($urlToAdd) {
                $arrayContent = trim($matches[1]);
                $arrayContent = !empty($arrayContent)
                    ? $arrayContent . ",\n        " . $urlToAdd
                    : "\n        " . $urlToAdd . "\n    ";
                return "public static array \$FilterUrls = [\n        {$arrayContent}\n    ];";
            }, $content);
            file_put_contents($file, $content);
        }
    }

    // Erfolgreiche Antwort: Anzahl der gelöschten Zeilen zurückgeben
    return response()->json([
        'success' => true,
        'deleted' => $deleted,
        'message' => "Statistik für '{$request->url}' wurde gelöscht."
    ]);

} catch (\Throwable $e) {
    \Log::error("Fehler beim Löschen der Statistik: " . $e->getMessage());
    return response()->json([
        'success' => false,
        'message' => "Fehler beim Löschen der Statistik."
    ], 500);
}
    }

    function killtimestamp(string $url): string
    {
        return $url;
        $decoded = ($url);
        $pattern = '/\d{4}-\d{2}-\d{2}%?\d{2}%3A\d{2}%3A\d{2}/';
        $clean = preg_replace($pattern, '', $decoded);
        return $clean;
    }

    public function SH($str)
    {
        if (!$str) return '/';

        $decoded = rawurldecode($str);
        $clean = str_replace([request()->getHost(), "https://", 'www.', "http://"], '', $decoded);

        if (substr_count($clean, "home/infos/show")) $clean = "/home/infos_show";
        if (substr_count($clean, "blogs/show")) $clean = "/blogs_show";
        if (substr_count($clean, "images/show/")) $clean = "images_show";
        if (substr_count($clean, "?page=")) $clean = str_replace("?page=" . @$_GET['page'], '', $clean);
        if (substr_count($clean, "?search=")) $clean = str_replace("?search=" . @$_GET['search'], '', $clean);
        if (substr_count($clean, "home/show/pictures/")) $clean = "/picures_show";

        return empty($clean) ? '/' : $clean;
    }

    public function dboard(Request $request)
    {
        $domm = strtolower($request->dom);
        $m = max((int)$request->month, 1);

        // "all" als KEIN Filter behandeln
        $isAll = ($domm === 'all' || $domm === '' || $domm === null);

        Log::info("DOM: {$domm}, Month: {$m}");

        $query = DB::connection('mariadb')
            ->table('xgen_page_views')
            ->where('visited_at', '>=', now()->subMonths($m));
            if (!CheckZRights("StatisticsAll")) {
                // Kein Recht → nur eigene Domain
                $query->whereRaw("TRIM(LOWER(dom)) = ?", [strtolower(SD())]);

            } elseif (!$isAll) {
                // Einzelne Domain gewählt
                $query->whereRaw("TRIM(LOWER(dom)) = ?", [trim($domm)]);
            }
            // sonst: ALL → kein WHERE → alle Domains
            Log::info("WHERE applied: dom = {$domm}");

        // $rawRows = DB::connection('mariadb')->table('xgen_page_views')->get();
        // Log::info("All DB rows: " . json_encode($rawRows));
        $rows = $query
            ->select('url', DB::raw('LOWER(dom) as dom'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('url', 'dom')
            ->orderBy('url', "ASC")
            ->get();

        Log::info("Rows fetched: " . count($rows));

        $rows = $rows->filter(function ($row) {
            foreach (Settings::$nostats as $ignore) {
                if ($ignore !== '' && str_contains($row->url, $ignore)) return false;
            }
            return true;
        })->sortBy('url')->values();

        Log::info("Rows after filter: " . count($rows));

        $labels = $rows->pluck('url')->unique()->values()->all();
        Log::info("Labels: " . json_encode($labels));

        $domColors = [
            'ab'  => '#4F86F7',
            'mfx' => '#FFA500',
            'dag' => '#E63946',
            'chh' => '#1B3A8A',
        ];

        if (!CheckZRights("StatisticsAll")) {
            $doms = [strtolower(SD())];

        } elseif (!$isAll) {
            $doms = [$domm];

        } else {
            // ALL → alle vorhandenen Domains
            $doms = $rows->pluck('dom')->unique()->values()->all();
        }

        Log::info("DOMs to display: " . json_encode($doms));

        $datasets = [];
        foreach ($doms as $dom) {
            $label = SD('1', $dom);
            $color = $domColors[$dom] ?? '#888888';

            $data = array_fill(0, count($labels), 0);
            foreach ($rows as $r) {
                if ($r->dom === $dom) {
                    $idx = array_search($r->url, $labels);
                    if ($idx !== false) $data[$idx] = (int)$r->cnt;
                }
            }

            $datasets[] = [
                'label' => $label,
                'data'  => $data,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'borderWidth' => 1,
            ];
        }

        Log::info("Datasets: " . json_encode($datasets));

        return response()->json([
            'labels' => array_values($labels),
            'datasets' => $datasets,
        ]);
    }

    public function stats()
    {
        $data = DB::connection('mariadb')
            ->table('xgen_page_views')
            ->select('url', DB::raw('COUNT(*) as views'))
            ->groupBy('url')
            ->orderBy('url', 'ASC')
            ->get();

        Log::info("Stats data: " . json_encode($data));

        return response()->json($data);
    }
}
