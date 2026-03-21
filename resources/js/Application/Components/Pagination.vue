S<template>

  <div v-if="links?.length > 1" class="flex justify-center gap-2">
    <button
      v-for="(link, i) in links"
      :key="i"
      :disabled="!link.url"
      @click="go(link)"
      class="px-3 py-1 border rounded lolink"
      :class="{
            'flex items-center font-bold border-primary-sun-500 text-primary-sun-900 dark:border-primary-night-500 dark:text-primary-night-900': link.active,
            'opacity-50 cursor-not-allowed': !link.url
      }"
      >
      <span v-if="link.label === 'pagination.previous'">« Zurück</span>
      <span v-else-if="link.label === 'pagination.next'">Weiter »</span>
      <span v-else v-html="link.label"></span>
    </button>
  </div>
</template>

<script>
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
go(link) {
    if (!link.url) return;

    const url = new URL(link.url, window.location.origin);

    const params = new URLSearchParams(url.search);
    const page = params.get('page');

    const searchParams = new URLSearchParams(window.location.search);
    const search = searchParams.get('search');

    let finalUrl = "/" + this.basePath + "?page=" + page;

    if (search) {
        finalUrl += "&search=" + encodeURIComponent(search);
    }

    this.$inertia.visit(finalUrl, {
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
//   this.$inertia.visit(this.basePath + url.pathname + url.search, {
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

//       this.$inertia.visit(url, {
//         preserveState: false,
//         replace: false,
//       });
//     },
  },
};
</script>
