@php
    use App\Http\Controllers\GlobalController;
    use App\Http\Controllers\DarkModeController;

    App::setLocale('de');
    setlocale(LC_ALL, 'deu_deu.1252');

    $subdomain = SD();
    $pagen = SD("pn");

    $favicon = "/images/_{$subdomain}/web/alogo.png";
    $ahost = $_SERVER['HTTP_HOST'];

    $_SESSION['comment_ids'] = [];
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script>
        window.subdomain = "{{ $subdomain }}";
        window.subdomain_alt = "{{ $sd_alt ?? '' }}";
        window.pagename = "{{ $pagen }}";
        window.ahost = "{{ $ahost }}";
        window.app_name = "{{ $pagen }}";
    </script>

    <link
        rel="icon"
        href="{{ $favicon }}"
        type="image/png"
    >

    <!-- CSS -->
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/photoswipe/photoswipe.css">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="/css/shariff.complete.css">
    <link rel="stylesheet" href="/css/tailw/{{ $subdomain }}.css">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">

    <!-- JS -->
    <script src="/js/app.js" type="module"></script>

    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/shariff.complete.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/users.js') }}"></script>

    <script src="/js/moment.min.js"></script>

    <style>

        /*
        |--------------------------------------------------------------------------
        | Scroll To Top Button
        |--------------------------------------------------------------------------
        */

        .back-to-top {

            position: fixed;

            right: 25px;
            bottom: 25px;

            width: 55px;
            height: 55px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9999px;

            background: #2563eb;
            color: #fff;

            font-size: 20px;

            text-decoration: none;

            z-index: 9999;

            cursor: pointer;

            box-shadow:
                0 4px 14px rgba(0,0,0,.25);

            transition:
                opacity .3s ease,
                transform .3s ease,
                background .3s ease;
        }

        .back-to-top:hover {

            background: #1d4ed8;

            color: #fff;

            transform: translateY(-4px);
        }

        .dark .back-to-top {

            background: #111827;
        }

        .dark .back-to-top:hover {

            background: #1f2937;
        }

    </style>

</head>

@php

    if (empty($_SESSION['dm'])) {

        DarkModeController::setDarkMode('dark');

        $_SESSION['dm'] = 'dark';
    }

    $dm = $_SESSION['dm'] ?? 'dark';

@endphp

<body class="{{ $dm }}">

    <input
        type="hidden"
        id="csrf-token"
        value="{{ csrf_token() }}"
    >

    @if(isset($page['props']['im_cont']))
        <div style="display:none;">

            @foreach($page['props']['im_cont'] as $img)

                <img
                    src="/images/{{ $img['fileName'] }}"
                    alt="{{ $img['label'] }}"
                >

            @endforeach

        </div>
    @endif

    <div id="app">

        @if ($subdomain == 'hm')

            @include('layouts.hm.hm_navigation')

        @else

            @include('layouts.navigation')

        @endif

        <main class="py-4 w10">

            @yield('content')

        </main>

        @if ($subdomain == 'hm')

            @include('layouts.hm.hm_subfooter')

        @else

            @include('layouts.subfooter')

        @endif

    </div>

    <!-- Scroll To Top -->
    <a
        href="#top"
        class="back-to-top"
        aria-label="Nach oben"
    >
        <i class="fas fa-arrow-up"></i>
    </a>

    <script>

        var $y = jQuery.noConflict();

        $y(document).ready(function() {

            /*
            |--------------------------------------------------------------------------
            | Scroll To Top
            |--------------------------------------------------------------------------
            */

            $y(".back-to-top").hide();

            $y(window).scroll(function() {

                if ($y(this).scrollTop() > 150) {

                    $y(".back-to-top").fadeIn();

                } else {

                    $y(".back-to-top").fadeOut();
                }
            });

            $y(".back-to-top").click(function(e) {

                e.preventDefault();

                $y("html, body").animate({

                    scrollTop: 0

                }, 700);

            });

        });

    </script>

    <!-- Optional -->
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <img
        src="{{ route('countpixel', [
            'url'   => urldecode(request()->fullUrl()),
            'route' => request()->route()?->getName() ?? 'unknown',
            'page'  => request()->query('page')
        ]) }}"
        alt=""
        width="1"
        height="1"
        style="display:none;"
    >

</body>

</html>
