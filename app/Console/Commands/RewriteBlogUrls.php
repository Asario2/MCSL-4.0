<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RewriteBlogUrls extends Command
{
    protected $signature =
        'blogs:rewrite-from-sitemap';

    protected $description =
        'Rewrite old URLs inside blogs table';

    public function handle(): int
    {
        /*
        |--------------------------------------------------------------------------
        | Columns
        |--------------------------------------------------------------------------
        */

        $columns = [

            'title',
            'short_text',
            'summary',
            'message',
            'text',
            'content',
            'description',
        ];

        /*
        |--------------------------------------------------------------------------
        | Blogs
        |--------------------------------------------------------------------------
        */

        $blogs = DB::table('blogs')->get();

        $updated = 0;

        foreach ($blogs as $blog) {

            $changes = [];

            foreach ($columns as $column) {

                if (
                    !isset($blog->$column)
                    || empty($blog->$column)
                ) {
                    continue;
                }

                $content = $blog->$column;

                $original = $content;

                /*
                |--------------------------------------------------------------------------
                | Decode HTML entities
                |--------------------------------------------------------------------------
                */

                $content = html_entity_decode(
                    $content,
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                /*
                |--------------------------------------------------------------------------
                | URL Decode
                |--------------------------------------------------------------------------
                */

                $content = urldecode($content);

                /*
                |--------------------------------------------------------------------------
                | Cleanup
                |--------------------------------------------------------------------------
                */

                $content = str_replace(
                    ['\\'],
                    '',
                    $content
                );

                $content = preg_replace(
                    '#<a\b[^>]*>\s*</a>#i',
                    '',
                    $content
                );
                /*
                |--------------------------------------------------------------------------
                | Images Rewrite
                |--------------------------------------------------------------------------
                */
                $content = preg_replace("#blogs/show/kontakt#i",'home/contacts',$content);
                $content = preg_replace("#http://ab.test.mcs/#ipe",'/',$content);

                $content = preg_replace_callback(
                    '#.*?blogs/show/(?!y)([a-zA-Z0-9_-]+)#siU',
                    function ($matches) {

                        return '/home/show/pictures/' . ucfirst($matches[1]);
                    },
                    $content
                );
                $content = preg_replace_callback(
                    '#.*?index\.php\?page=images(?:&|&amp;)action=([a-z0-9_-]+)#i',
                    function ($matches) {

                        $slug = preg_replace('/\d+$/', '', $matches[1]);

                        return url(
                            '/home/show/pictures/' . ucfirst($slug)
                        );
                    },
                    $content
                );

                $content = preg_replace_callback(
                    '#/home/contacts#i',
                    function ($matches) {



                        return url(
                            'Kontakt'
                        );
                    },
                    $content
                );
                /*
                |--------------------------------------------------------------------------
                | show/images
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/images#i',
                    '/home/pictures',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | kontakt typo
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#konatkt#i',
                    '/home/contacts',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | Ton -> Keramik
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/ton#i',
                    '/home/show/pictures/Keramik',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | User
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/user#i',
                    '/home/users',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | Fineliner
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/fineliner#i',
                    '/home/show/pictures/Fineliner',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | DidYouKnow
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/didyouknow#i',
                    '/home/didyouknow',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | Ausgemaltes
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/ausgemaltes#i',
                    '/home/show/pictures/Ausgemaltes',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | 3DPrinter
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#show/3DPrinter#i',
                    '/home/show/pictures/3DPrinter',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | Shortstories
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#index\.php\?page=shortstories#i',
                    '/home/shortstories',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | Remove duplicate slashes
                |--------------------------------------------------------------------------
                */

                $content = preg_replace(
                    '#(?<!:)//+#',
                    '/',
                    $content
                );

                /*
                |--------------------------------------------------------------------------
                | Save changes
                |--------------------------------------------------------------------------
                */

                if (
                    $content !== $original
                ) {

                    $changes[$column]
                        = $content;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Update blog
            |--------------------------------------------------------------------------
            */

            if (!empty($changes)) {

                DB::table('blogs')
                    ->where('id', $blog->id)
                    ->update($changes);

                $updated++;

                $this->info(
                    "Updated Blog ID: {$blog->id}"
                );
            }
        }

        $this->info(
            "Finished. Updated {$updated} blog entries."
        );

        return self::SUCCESS;
    }
}
