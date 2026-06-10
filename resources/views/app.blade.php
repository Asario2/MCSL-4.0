<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ALL);

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GlobalController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\Settings;

App::setLocale('de');

$subdomain = SD();
$pagen = SD("pn");

$favicon = "/images/_{$subdomain}/web/alogo.png";

$ahost = request()->getHost();
$chost = request()->getSchemeAndHttpHost();

$glo = new GlobalController();

if (!file_exists(public_path($favicon))) {
    $favicon = "/images/favicon_default.png";
}

if (isset($_GET['re']) && $_GET['re'] === '1') {

    header(
        "Location: http://"
        . $_SERVER['HTTP_HOST']
        . str_replace("re=1", '', $_SERVER['REQUEST_URI'])
    );

    exit;
}
?>

<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title inertia>
        {{ ucf(CleanTable(1)) }}
    </title>

    <link
        rel="canonical"
        href="{{ url()->current() }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
    >

    <script>

        if (typeof global === 'undefined') {
            window.global = window;
        }

        window.Laravel = {
            userId: {{ auth()->id() ?? 'null' }}
        };

        window.authid = "{{ Auth::id() }}";

        window.subdomain = "{{ $subdomain }}";

        window.subdomain_alt = "{{ $sd_alt ?? '' }}";

        window.pagename = "{{ $pagen }}";

        window.ahost = "{{ $ahost }}";

        window.chost = "{{ $chost }}";

        window.app_name = "{{ $pagen }}";

    </script>

    <link
        rel="icon"
        type="image/png"
        href="{{ $favicon }}"
    >

    <script src="/js/jquery-3.6.0.min.js"></script>

    <script src="/js/users.js"></script>

<script>
(function () {

    const forceLight =
        @json(
            SD() == "chh"
            || in_array(
                request()->path(),
                Settings::$loginpages
            )
        );

    if (forceLight) {

        document.documentElement
            .classList
            .remove('dark');

        return;
    }

    let theme =
        localStorage.getItem('theme');

    /*
    |--------------------------------------------------------------------------
    | Default = Dark
    |--------------------------------------------------------------------------
    */

    if (!theme) {

        theme = 'dark';

        localStorage.setItem(
            'theme',
            'dark'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Theme anwenden
    |--------------------------------------------------------------------------
    */

    if (theme === 'dark') {

        document.documentElement
            .classList
            .add('dark');

    } else {

        document.documentElement
            .classList
            .remove('dark');
    }

})();

    </script>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        href="/css/tailw/extra.css?time={{ time() }}"
        rel="stylesheet"
    >

    <link
        href="/css/tailw/{{ $sd_alt ?? 'default' }}.css?time={{ time() }}"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="/Shariff/shariff.complete.css"
    >

    <script src="/Shariff/shariff.min.js"></script>

    @inertiaHead

    @if (!request()->is('home/privacy'))
    {!! CookieConsent::styles() !!}
    @endif

    <script>

    (function () {

        function handleReload() {

            const url =
                new URL(window.location.href);

            if (
                url.searchParams.get('re') === '1'
            ) {

                url.searchParams.delete('re');

                window.location.replace(
                    url.toString()
                );
            }
        }

        if (
            document.readyState === 'loading'
        ) {

            document.addEventListener(
                'DOMContentLoaded',
                handleReload
            );

        } else {

            handleReload();
        }

    })();

    </script>

</head>

<body class="font-sans antialiased  ">

    <input
        type="hidden"
        id="token"
        value="{{ csrf_token() }}"
    >

    <script type="module">

    import {
        reactive
    } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js';

    const toastBus =
        reactive({
            toasts: []
        });

    toastBus.toasts.push({
        message: 'Hallo',
        type: 'success',
        duration: 5000
    });

    </script>

    @inertia

    {!! CookieConsent::scripts(options: [


        'cookie_lifetime' =>
            config(
                'laravel-cookie-consent.cookie_lifetime',
                7
            ),

        'reject_lifetime' =>
            config(
                'laravel-cookie-consent.reject_lifetime',
                1
            ),

        'disable_page_interaction' =>
            config(
                'laravel-cookie-consent.disable_page_interaction',
                true
            ),

        'preferences_modal_enabled' =>
            config(
                'laravel-cookie-consent.preferences_modal_enabled',
                true
            ),

        'consent_modal_layout' =>
            config(
                'laravel-cookie-consent.consent_modal_layout',
                'bar-inline'
            ),

        'flip_button' =>
            config(
                'laravel-cookie-consent.flip_button',
                true
            ),

        'theme' =>
            config(
                'laravel-cookie-consent.theme',
                'default'
            ),

        'cookie_prefix' =>
            config(
                'laravel-cookie-consent.cookie_prefix',
                'Laravel_App'
            ),

        'policy_links' => [

            [
                'text' =>
                    CookieConsent::translate(
                        'Privacy Policy'
                    ),

                'link' =>
                    url('/home/privacy')
            ],

        ],

    ]) !!}

    <script>

    @if(session('force_reload'))

        sessionStorage.setItem(
            "force_reload",
            "true"
        );

    @endif

    </script>

    <script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            if (window?.toastBus) {

                @php
                    echo Notify();
                @endphp

            } else {

                console.error(
                    "toastBus existiert noch nicht"
                );
            }

        }
    );

    </script>

    <style>

        .cc-window {
            display: inline-block !important;
        }

    </style>

    <img
        width="1"
        height="1"
        src="{{ route('countpixel', [
            'url'   => rawurldecode(request()->fullUrl()),
            'route' => request()->route()?->getName() ?? 'unknown',
            'page'  => request()->query('page')
        ]) }}"
    >

    <!-- Scroll To Top -->

    <!-- Scroll To Top -->
{{-- <a
    href="#top"
    class="back-to-top"
    id="scrollTopBtn"
    aria-label="Nach oben"
    style="display:none;"
>
    <i class="fas fa-arrow-up"></i>
</a> --}}

<a
    href="#top"
    id="scrollTopBtn"
      class="
        fixed
        bottom-6
        right-6
        w-12
        h-12
        hidden
        items-center
        justify-center
        rounded-lg
        bg-black
        hover:bg-blue-700
        text-xl
        shadow-lg
        transition-all
        duration-300
        z-[9999]
    "
    style="color:#FFF !important; border:2px solid #fff !important;box-shadow:0px 0px 3px 2.5px Black !important;"
>
<!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
<svg xmlns="http://www.w3.org/2000/svg" fill='currentColor' class='w-7 h-7' viewBox="0 0 448 512"><path d="M201.4 105.4c12.5-12.5 32.8-12.5 45.3 0l192 192c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 173.3 54.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l192-192z"/></svg>
</a>
<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const button =
        document.getElementById(
            'scrollTopBtn'
        );

    if (!button) {

        console.error(
            'ScrollTop Button nicht gefunden'
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Button Visibility
    |--------------------------------------------------------------------------
    */

    function toggleButton() {

        if (window.scrollY > 200) {

            button.classList.remove(
                'hidden'
            );

            button.classList.add(
                'flex'
            );

        } else {

            button.classList.remove(
                'flex'
            );

            button.classList.add(
                'hidden'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Initial Check
    |--------------------------------------------------------------------------
    */

    toggleButton();

    /*
    |--------------------------------------------------------------------------
    | Scroll Event
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'scroll',
        toggleButton
    );

    /*
    |--------------------------------------------------------------------------
    | Smooth Scroll To Top
    |--------------------------------------------------------------------------
    */

    button.addEventListener(
        'click',
        function (e) {

            e.preventDefault();

            window.scrollTo({

                top: 0,

                behavior: 'smooth'

            });

        }
    );

});

</script>
    <script>

</script>
</body>

</html>
