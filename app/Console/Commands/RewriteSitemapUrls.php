<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RewriteSitemapUrls extends Command
{
    /*
    |--------------------------------------------------------------------------
    | Command Signature
    |--------------------------------------------------------------------------
    */

    protected $signature = 'sitemap:rewrite';

    /*
    |--------------------------------------------------------------------------
    | Command Description
    |--------------------------------------------------------------------------
    */

    protected $description =
        'Rewrite old sitemap URLs to clean Laravel URLs';

    /*
    |--------------------------------------------------------------------------
    | Execute Command
    |--------------------------------------------------------------------------
    */

    public function handle(): int
    {
        /*
        |--------------------------------------------------------------------------
        | Sitemap Path
        |--------------------------------------------------------------------------
        */

        $path = public_path(
            'sitemap.ab.xml'
        );

        if (!file_exists($path)) {

            $this->error(
                'sitemap.ab.xml not found.'
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Load XML
        |--------------------------------------------------------------------------
        */

        libxml_use_internal_errors(true);

        $xml = simplexml_load_file($path);

        if (!$xml) {

            $this->error(
                'Invalid XML sitemap.'
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Rewrite URLs
        |--------------------------------------------------------------------------
        */

        foreach ($xml->url as $urlNode) {

            if (!isset($urlNode->loc)) {
                continue;
            }

            $url = (string)$urlNode->loc;
            $this->info("url:".$url);
            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            $url = urldecode($url);

            $url = str_replace(
                ['\\', '"', "'"],
                '',
                $url
            );

            $url = preg_replace_callback(
                '#index\.php\?page=images&action=([a-z0-9_-]+)#i',
                function ($matches) {

                    $slug = preg_replace(
                        '/\d+$/',
                        '',
                        $matches[1]
                    );

                    return url(
                        '/home/show/pictures/'
                        . ucfirst($slug)
                    );
                },
                $url
            );

            /*
            |--------------------------------------------------------------------------
            | Blog
            |--------------------------------------------------------------------------
            */

            $url = preg_replace_callback(
                '#/show/images#i',
                function ($matches) {

                    return url(
                        '/home/pictures/'
                        // . ucfirst(
                        //     urldecode(
                        //         $matches[1]
                            // )
                        // )
                    );
                },
                $url
            );

            /*
            |--------------------------------------------------------------------------
            | News
            |--------------------------------------------------------------------------
            */

            $url = preg_replace_callback(
                '#konatkt#i',
                function ($matches) {

                    return url(
                        '/home/contacts/'

                    );
                },
                $url
            );

            /*
            |--------------------------------------------------------------------------
            | Galleries
            |--------------------------------------------------------------------------
            */

            $url = preg_replace_callback(
                '#show/ton#i',
                function ($matches) {

                    return url(
                        '/home/show/pictures/Keramik'

                    );
                },
                $url
            );

            $url = preg_replace_callback(
                '#show/user#i',
                function ($matches) {

                    return url(
                        '/home/users'

                    );
                },
                $url
            );

            $url = preg_replace_callback(
                '#show/fineliner#i',
                function ($matches) {

                    return url(
                        '/home/show/pictures/Fineliner'

                    );
                },
                $url
            );
            $url = preg_replace_callback(
                '#show/didyouknow#i',
                function ($matches) {

                    return url(
                        '/home/didyouknow'

                    );
                },
                $url
            );
            $url = preg_replace_callback(
                '#show/ausgemaltes#i',
                function ($matches) {

                    return url(
                        '/home/show/pictures/Ausgemaltes'

                    );
                },
                $url
            );
            $url = preg_replace_callback(
                '#show/3DPrinter#i',
                function ($matches) {

                    return url(
                        '/home/show/pictures/3DPrinter'

                    );
                },
                $url
            );
            $url = preg_replace_callback(
                '#index.php?page=shortstories#i',
                function ($matches) {

                    return url(
                        '/home/shortstories'

                    );
                },
                $url
            );
            /*
            |--------------------------------------------------------------------------
            | Generic Fallback
            |--------------------------------------------------------------------------
            */

            // $url = preg_replace_callback(
            //     '#index\.php\?page=([^&]+)&action=([^&]+)#i',
            //     function ($matches) {

            //         return url(
            //             '/'
            //             . strtolower(
            //                 urldecode(
            //                     $matches[1]
            //                 )
            //             )
            //             . '/'
            //             . ucfirst(
            //                 urldecode(
            //                     $matches[2]
            //                 )
            //             )
            //         );
            //     },
            //     $url
            // );

            /*
            |--------------------------------------------------------------------------
            | Remove duplicate slashes
            |--------------------------------------------------------------------------
            */

            $url = preg_replace(
                '#(?<!:)//+#',
                '/',
                $url
            );

            /*
            |--------------------------------------------------------------------------
            | Remove index.php from path URLs
            |--------------------------------------------------------------------------
            */

            $url = preg_replace(
                '#/index\.php/#i',
                '/',
                $url
            );

            /*
            |--------------------------------------------------------------------------
            | Save URL back
            |--------------------------------------------------------------------------
            */

            $urlNode->loc = $url;
        }

        /*
        |--------------------------------------------------------------------------
        | Save XML
        |--------------------------------------------------------------------------
        */

        $result = $xml->asXML($path);

        if (!$result) {

            $this->error(
                'Failed to save sitemap.'
            );

            return self::FAILURE;
        }

        $this->info(
            'Sitemap URLs rewritten successfully.'
        );

        return self::SUCCESS;
    }
}
