<template>
    <div
        v-if="links?.length > 3"
        class="flex w-full max-w-[98%] justify-start md:justify-center gap-2 overflow-x-auto overflow-y-hidden ml-[10px] px-1"
    >
        <a
            v-for="(link, i) in links"
            :key="i"
            :href="buildUrl(link)"
            @click.prevent="go(link)"
            class="shrink-0 whitespace-nowrap px-3 py-1 border rounded lolink"
            :class="{
                'flex items-center font-bold border-primary-sun-500 text-primary-sun-900 dark:border-primary-night-500 dark:text-primary-night-500': link.active,
                'opacity-50 cursor-not-allowed': !link.url
            }"
        >
            <span
                v-if="link.label === 'pagination.previous'"
                class="text-primary-sun-900 dark:text-primary-night-500"
            >
                « Zurück
            </span>

            <span
                v-else-if="link.label === 'pagination.next'"
                class="text-primary-sun-900 dark:text-primary-night-900"
            >
                Weiter »
            </span>

            <span
                v-else
                class="text-primary-sun-900 dark:text-primary-night-900"
                v-html="link.label"
            ></span>
        </a>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
export default {
  props: {
    links: { type: Array, required: true },
    basePath: { type: String, required: true },
  },

  data()
  {
    return {
        searchString:'',
    }
  },

  methods: {

    // 🔥 SSR + SEO URL Builder
    buildUrl(link) {
        if (!link.url) return '#';

        try {
            const url = new URL(link.url, 'http://dummy'); // SSR-safe
            const params = new URLSearchParams(url.search);
            const page = params.get('page');
            let base = this.basePath.replace(/\/$/, '');

            const amp = "?";
            let finalUrl = "/" + base + amp + "page=" + page;

            // Client-only search übernehmen
            if (typeof window !== 'undefined') {
                const searchParams = new URLSearchParams(window.location.search);
                const search = searchParams.get('search');

                if (search) {
                    finalUrl += "&search=" + encodeURIComponent(search);
                }
            }

            return finalUrl;

        } catch (e) {
            return '#';
        }
    },

    go(link) {
        if (!link.url) return;

        const finalUrl = this.buildUrl(link);

        router.visit(finalUrl, {
            preserveState: false,
            replace: false,
        });
    },

// OLD GO
//     go(link) {
//   if (!link.url) return;

//   const url = new URL(link.url, window.location.origin);

//   const params = new URLSearchParams(window.location.search);
//   if (params.has('search')) {
//     url.searchParams.set('search', params.get('search'));
//   }
// //   alert(this.basePath);
// //   return "";
//   router.visit(this.basePath + url.pathname + url.search, {
//     preserveState: false,
//   });
// }


// old go end

    //     go(link) {
//       if (!link.url) return;

//       // basePath z.B. "/home/show/pictures/acryl"
//       // link.url z.B. "/?page=2"
//         const params = new URLSearchParams(window.location.search);
//         const search = params.get('search');
// if(search)
// {
//     this.searchString = '&search=' + search;
// }
// else{
//     this.searchString = '';
// }
//         const url = this.basePath + link.url + this.searchString;

//       router.visit(url, {
//         preserveState: false,
//         replace: false,
//       });
//     },

  },
};
</script>
