<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use \App\Models\Settings;
use \App\Models\FilterUrls;
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
    ];

    public static array $nostats = [
        '/admin',
        '/_debug',
        '/api/',
        'login',
        'pm/index',
        'mail/subscr',
    ];
    public function track(Request $request,$url, $route, $page = null)
    {

        $url = rawurldecode(
            base64_decode($url)
        );

        try {

            $userAgent = strtolower($request->userAgent() ?? '');

            if (
                !str_contains($userAgent, 'chrome') &&
                !str_contains($userAgent, 'firefox') &&
                !str_contains($userAgent, 'safari') &&
                !str_contains($userAgent, 'edg')
            ) {

                // \Log::info('TRACK STOP: USER AGENT', [
                //     'ua' => $userAgent
                // ]);

                return $this->pixelResponse();
            }

            $host = $request->getHost();

            if ($host === 'mail.marblefx.net') {

                // \Log::info('TRACK STOP: MAIL HOST');

                return $this->pixelResponse();
            }

            $routeName = $route;

            // \Log::info('TRACK ROUTE CHECK', [
            //     'route' => $routeName,
            //     'exists' => Route::getRoutes()->getByName($routeName) !== null,
            // ]);

            if (
                $routeName &&
                Route::getRoutes()->getByName($routeName) === null
            ) {

                // \Log::info('TRACK STOP: INVALID ROUTE', [
                //     'route' => $routeName
                // ]);

                return $this->pixelResponse();
            }

            foreach ($this->excludeRoutes as $pattern) {

                if (fnmatch($pattern, $routeName)) {

                    // \Log::info('TRACK STOP: EXCLUDED ROUTE', [
                    //     'route' => $routeName,
                    //     'pattern' => $pattern,
                    // ]);

                    return $this->pixelResponse();
                }
            }

            $rawUrl = $this->SH($url) ?? '/';

            // \Log::info('TRACK URL', [
            //     'rawUrl' => $rawUrl
            // ]);

            if (str_contains($rawUrl, '?')) {

                parse_str(
                    parse_url($rawUrl, PHP_URL_QUERY) ?? '',
                    $params
                );

                // \Log::info('TRACK PARAMS', [
                //     'params' => $params
                // ]);

                foreach (array_keys($params) as $param) {

                    if (!in_array($param, ['page', 'search'])) {

                        // \Log::info('TRACK STOP: INVALID PARAM', [
                        //     'param' => $param,
                        //     'url' => $rawUrl,
                        // ]);

                        return $this->pixelResponse();
                    }
                }
            }

            foreach ($this->excludeURLs as $pattern) {

                if (fnmatch($pattern, $rawUrl)) {

                    // \Log::info('TRACK STOP: EXCLUDED URL', [
                    //     'url' => $rawUrl,
                    //     'pattern' => $pattern,
                    // ]);

                    return $this->pixelResponse();
                }
            }

            foreach (FilterUrls::$FilterUrls as $pattern) {

                if (fnmatch($pattern, $rawUrl) && $rawUrl != '/') {

                    // \Log::info('TRACK STOP: FILTER URL', [
                    //     'url' => $rawUrl,
                    //     'pattern' => $pattern,
                    // ]);

                    return $this->pixelResponse();
                }
            }

            $routeAction =
                Route::getRoutes()
                    ->getByName($routeName)
                    ?->action ?? [];

            $middlewares =
                $routeAction['middleware'] ?? [];

            $middlewares = is_array($middlewares)
                ? $middlewares
                : [$middlewares];

            // \Log::info('TRACK MIDDLEWARES', [
            //     'middlewares' => $middlewares
            // ]);

            if (in_array('auth', $middlewares)) {

                // \Log::info('TRACK STOP: AUTH ROUTE');

                return $this->pixelResponse();
            }

            $ip = $request->ip();

            if (
                filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4
                )
            ) {

                $parts = explode('.', $ip);
                $parts[3] = '0';
                $ip = implode('.', $parts);

            } elseif (
                filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV6
                )
            ) {

                $parts = explode(':', $ip);
                $parts[count($parts) - 1] = '0000';
                $ip = implode(':', $parts);
            }

            \Log::info('SAVE PAGEVIEW SUCCESS', [
                'dom'   => SD(),
                'url'   => $rawUrl,
                'route' => $routeName,
                'ip'    => $ip,
            ]);

            PageView::create([
                'dom'        => SD(),
                   'url'        => $rawUrl,
                'ip'         => $ip,
                'visited_at' => now(),
            ]);

            // \Log::info('TRACK SUCCESS');

        } catch (\Throwable $e) {

            \Log::error('COUNTPIXEL EXCEPTION', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                "url"=>$rawUrl,
                "visited_at"=>now(),
            ]);
        }
        // \Log::info('FINAL ROWS', [
        //     'count' => $urlRows->count(),
        //     'picures_show' => $urlRows
        //         ->where('url', '/picures_show')
        //         ->values()
        //         ->all()
        // ]);
        return $this->pixelResponse();
    }

    public static function o404()
    {
        $path = request()->getPathInfo();

        // ❗ nur löschen wenn wirklich 404 typische URL
        if (str_contains($path, 'api') || str_contains($path, 'admin')) {
            return;
        }

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
        if(!CheckZRights("Statistics"))
        {
            return response()->json([
                'success' => false,
                'deleted' => 1,
                'message' => "Statistik für '{$url}' wurde gelöscht."
            ]);
        }
        try {
            $url = $request->input('url');

            if (is_array($url)) {
                $dom = $url['dom'] ?? SD();
                $url = $url['url'] ?? '/';
            } else {
                $dom = $request->input('dom', SD());
            }

            $url = $this->killtimestamp($url);
//     \Log::info("DELETE URL: {$url} | dom: {$request->dom}");

    $dom = $request->dom ?? SD();
\Log::info('DELETE DEBUG', [
    'url' => $url,
    'dom' => $dom,
    'url_type' => gettype($url),
    'dom_type' => gettype($dom),
]);
   $query = DB::connection('mariadb')
    ->table('xgen_page_views')
    ->where(function ($q) use ($url) {

        $q->where('url', '/' . ltrim($url, '/'))
          ->orWhere('url', ltrim($url, '/'))
          ->orWhere('url', $url);

    })
    ->where('dom', $dom);

    $deleted = $query->delete();
    \Log::info("Deleted rows: {$deleted}");

    // Optional: dauerhaft in FilterUrls speichern
    if ($request->save) {
        $file = app_path('Models/FilterUrls.php');
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
        'message' => "Statistik für '{$url}' wurde gelöscht."
    ]);

} catch (\Throwable $e) {
    \Log::error("Fehler beim Löschen der Statistik: " . $e->getMessage());
    return response()->json([
        'success' => false,
        'message' => "Fehler beim Löschen der Statistik."
    ], 500);
}
    }
    function TrailSlash($txt)
    {
        if(str_starts_with($txt,"/"))
        {
            return ltrim($txt,"/");
        }
        return $txt;
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
        if (substr_count($clean, "images/show/")) $clean = "/home/  images_show";
        if (substr_count($clean, "&page=")) $clean = str_replace("&page=" . @$_GET['page'], '', $clean);
        if (substr_count($clean, "?page=")) $clean = str_replace("?page=" . @$_GET['page'], '', $clean);
        if (substr_count($clean, "?search=")) $clean = str_replace("?search=" . @$_GET['search'], '', $clean);
        if (substr_count($clean, "home/show/pictures")) $clean = "/home/pictures_show";
        if (substr_count($clean, "home/users/show/")) $clean = "/home/users_show";

        if (!empty($clean) && $clean !== '/' && !str_starts_with($clean, '/')) {
            $clean = '/' . $clean;
        }

        return empty($clean) ? '/' : $clean;
    }

    public function dboard(Request $request)
    {
        $domm = strtolower($request->dom);
        $m = max((int)$request->month, 1);

        // "all" als KEIN Filter behandeln
        $isAll = ($domm === 'all' || $domm === '' || $domm === null);

//         Log::info("DOM: {$domm}, Month: {$m}");

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
//             Log::info("WHERE applied: dom = {$domm}");

        // $rawRows = DB::connection('mariadb')->table('xgen_page_views')->get();
        // Log::info("All DB rows: " . json_encode($rawRows));
        $rows = $query->select(
            DB::raw("
                CASE
                    WHEN url = '/' THEN '/'
                    WHEN LEFT(url, 1) = '/' THEN url
                    ELSE CONCAT('/', url)
                END AS url
            "),
            DB::raw('LOWER(dom) as dom'),
            DB::raw('COUNT(*) as cnt')
        )
        ->groupBy('url', 'dom')
        ->orderBy('url', 'ASC')
        ->get();

            // \Log::info(
            //     'DOM COUNTS',
            //     $rows->groupBy('dom')->map->count()->toArray()
            // );

//         Log::info("Rows fetched: " . count($rows));

        $rows = $rows->filter(function ($row) {
            foreach (Settings::$nostats as $ignore) {
                if ($ignore !== '' && str_contains($row->url, $ignore)) return false;
            }
            return true;
        })->sortBy('url')->values();

        // /home als / behandeln und Werte zusammenfassen

        $rows = $rows
            ->map(function ($row) {
                if ($row->url === '/home') {
                    $row->url = '/';
                }
                return $row;
            })
            ->groupBy(function ($row) {
                return $row->url . '|' . $row->dom;
            })
            ->map(function ($group) {
                $first = $group->first();

                $first->cnt = $group->sum('cnt');

                return $first;
            })
            ->values();


//         Log::info("Rows after filter: " . count($rows));

        $labels = $rows->pluck('url')->unique()->values()->all();
//         Log::info("Labels: " . json_encode($labels));

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

//         Log::info("DOMs to display: " . json_encode($doms));
        // \Log::info('PICURES ROWS', [
        //     'rows' => $rows
        //         ->where('url', '/picures_show')
        //         ->values()
        //         ->all()
        // ]);
        $datasets = [];
        foreach ($doms as $dom) {
            $label = SD('1', $dom);
            $color = $domColors[$dom] ?? '#888888';

            $data = array_fill(0, count($labels), 0);
            foreach ($rows as $r) {
                if ($r->dom === $dom) {
                    $idx = array_search($r->url, $labels);
                    if ($idx !== false) $data[$idx] += (int)$r->cnt;
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

//         Log::info("Datasets: " . json_encode($datasets));

        $urlRows = $rows
    ->map(function ($row) {
        return [
            'url' => $row->url,
            'dom' => $row->dom,
            'cnt' => $row->cnt,
        ];
    })
    ->sortBy(function ($row) {
        return strtolower($row['url']);
    })
    ->values();

        $labels = array_map(function ($url) {
            return $url === '/home' ? '/' : $url;
        }, $labels);

        $urlRows = $urlRows->map(function ($row) {

            if ($row['url'] === '/home') {
                $row['url'] = '/';
            }

            return $row;
        })->values();


        return response()->json([
            'labels' => array_values($labels),
            'datasets' => $datasets,
            'rows' => $urlRows,
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

//         Log::info("Stats data: " . json_encode($data));

        return response()->json($data);
    }
}
