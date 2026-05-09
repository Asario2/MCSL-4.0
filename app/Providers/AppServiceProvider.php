<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Tighten\Ziggy\Ziggy;

use App\Http\Controllers\GlobalController;
use App\Models\Settings;

// Optional (falls du es wieder aktivierst)
// use Whitecube\LaravelCookieConsent\LaravelCookieConsent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🧠 Helpers laden
        require_once app_path('helpers.php');
        Inertia::share([
            'sd' => fn () => SD(), // deine PHP SD Funktion
             'auth' => [
             'user' => fn () => auth()->user(),
             'app_url' => fn () => request()->getSchemeAndHttpHost(),
    ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🍪 Cookie Consent (optional / aktuell deaktiviert)
        |--------------------------------------------------------------------------
        */

        // app()->singleton(LaravelCookieConsent::class, function ($app) {
        //     $consent = new LaravelCookieConsent();
        //     $consent->setCookieDomain(request()->getHost());
        //     return $consent;
        // });

        // Config::set('cookieconsent.cookie.domain', request()->getHost());

        // app(CookieConsent::class)->useCookieName(
        //     'cookie_consent_' . str_replace('.', '_', request()->getHost())
        // );

        // app(LaravelCookieConsent::class)->useCookieName(
        //     'cookie_consent_' . str_replace('.', '_', request()->getHost())
        // );

        // $host = str_replace('.', '_', request()->getHost());
        // app(CookieConsentManager::class)->useCookieName('cookie_consent_' . $host);

        // $host = request()->getHost();
        // $slug = str_replace(['.', ':'], '_', $host);
        // CookiesManager::useCookieName('cookie_consent_' . $slug);

        // CookieConsent::useCookieName('cookie_consent_' . $host);

        /*
        |--------------------------------------------------------------------------
        | 🌍 Domain / Subdomain Handling
        |--------------------------------------------------------------------------
        */

        $host = request()->getHost();
        $subb = explode('.', str_replace("www.", '', $host))[0];

        switch ($subb) {
            case "asario":         $subb = "ab"; break;
            case "monikadargies":  $subb = "dag"; break;
            case "marblefx":       $subb = "mfx"; break;
            case "mjs":            $subb = "mjs"; break;
            case "ra-c-henning":   $subb = "chh"; break;
            case "217":            $subb = "mfx"; break;
            default: break;
        }

        $subb2 = $subb;
        $url = request()->getRequestUri();

        if (
            str_contains($url, "/login") ||
            str_contains($url, "/admin/") ||
            str_contains($url, "reset-password") ||
            str_contains($url, "register") ||
            str_contains($url, "forgot-password") ||
            str_contains($url, "email/verify") ||
            str_contains($url, "confirm-password") ||
            str_contains($url, "verify-email")
        ) {
            $subb2 = "ab";
        }

        // Globale View Variablen
        View::share('subdomain', $subb);
        View::share('sd_alt', $subb2);

        /*
        |--------------------------------------------------------------------------
        | ⚙️ Domain Settings
        |--------------------------------------------------------------------------
        */

        GlobalController::SetDomain();

        $domSettings = Settings::$dom;
        config([
            'app.name_alt' => $domSettings[SD()] ?? 'default.domain.com'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ⚡ Inertia + Ziggy (SSR FIX)
        |--------------------------------------------------------------------------
        */

        Inertia::share([
            'ziggy' => function () {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => request()->url(),
                ]);
            },
        ]);
    }
}
