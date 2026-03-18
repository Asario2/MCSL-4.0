<template>
    <Layout>
        <MetaHeader :title="'Ein/Ausgaben'" />

        <template #header>
            <breadcrumb :breadcrumbs="breadcrumbs" />
        </template>



        <div class="overflow-x-auto">
            <newbtn table="ausgaben" />
            <table class="min-w-full border-collapse">

                <thead>
                    <tr class="bg-layout-sun-200 dark:bg-layout-night-200">
                        <th class="text-left p-3 border-b-2 border-layout-sun-300 dark:border-layout-night-300">
                            Name
                        </th>
                        <th class="text-left p-3 border-b-2 border-layout-sun-300 dark:border-layout-night-300">
                            Typ
                        </th>
                        <th class="text-left p-3 border-b-2 border-layout-sun-300 dark:border-layout-night-300">
                            Betrag
                        </th>
                        <th class="text-right p-3 border-b-2 border-layout-sun-300 dark:border-layout-night-300">
                            Aktionen
                        </th>
                    </tr>
                </thead>

                <tbody>

                <tr
                v-for="item in entries_in"
                :key="item.id"
                :class="[
                    'border-b border-layout-sun-300 dark:border-layout-night-300', // Border bleibt
                    item.plus_minus === 'Einnahme'
                    ? 'bg-green-200 hover:bg-green-200/50 dark:bg-green-800 dark:hover:bg-green-800/50'
                    : item.plus_minus === 'Ausgabe'
                        ? 'bg-red-200 hover:bg-red-200/50 dark:bg-red-800 dark:hover:bg-red-800/50'
                        : 'bg-layout-sun-100 dark:bg-layout-night-100'
                ]"
                >
                        <td class="p-3">
                            <span v-html="item.name"></span>
                        </td>

                        <td class="p-3">
                            {{ item.plus_minus }}
                        </td>

                        <td class="p-3">
                            {{ item.cur_amount }} &euro;
                        </td>

                        <td class="p-3 text-right">
                            <editbtns :id="item?.id" table="ausgaben" />
                        </td>
                    </tr>
                </tbody>

            </table>
            Einnahmen Gesamt: {{ res.res_in }} &euro; ||| Ausgaben Gesamt: {{res.res_out}} &euro; ||| Rest: {{ res.res_total }} &euro;

        </div>

    </Layout>
</template>


<script>
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import PhotoSwipeLightbox from 'photoswipe/dist/photoswipe-lightbox.esm.js';
// import Pagination from "@/Application/Components/Pagination.vue";
import 'photoswipe/dist/photoswipe.css'
import {stripTags} from "@/helpers";
// import ZoomImage from "@/Application/Components/Content/ZoomImage.vue";
// import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
// import RatingWrapper from "@/Application/Components/Social/RatingWrapper.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import newbtn from "@/Application/Components/Form/newbtn.vue";
// import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
// import IconCamera from "@/Application/Components/Icons/Camera.vue";
import mapValues from "lodash/mapValues";
// import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
// import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
// import PhotoSwipeLightbox from "photoswipe/lightbox";
// import 'photoswipe/dist/phot oswipe.css';

// import { onMounted } from "vue";
// import PhotoSwipeLightbox from "photoswipe/lightbox";
// import "photoswipe/style.css";


import he from "he";
import BackBtn from "@/Application/Components/Form/BackBtn.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import { CleanTable } from '@/helpers';
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
export default {
    name:"Ein_Ausgaben",
  components: {
    Layout,
    MetaHeader,
    editbtns,
    newbtn,
    Breadcrumb,
  },
  props: {
    entries_in: {
    type: Object,
    required: true,
  },
  res: {
    type: Object,
    required: true,
  },
    ocont: {
      type: [Array, Object],
      default: () => [],
    },
    ratings: {
      type: Object,
      default: () => ({}),
    },
    createOn: {
      default: true,
    },
    breadcrumbs: {
        type: Object,
        default: () => ({}),
    },
    searchFilter: {
      type: Boolean,
      default: true,
    },
    routeCreate: {
      type: String,
      default: '',
    },

    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
  },
  data() {
    return {
      lightbox: null,
      openIndex: null,
      form: {
                search: this.filters?.search ?? "",
                },
      searchterm : this.filters?.search ?? "",
      statusColorsMap: {
        Einnahme: 'bg-green-200 dark:bg-green-800',
        Ausgabe: 'bg-red-200 dark:bg-red-800',
        Neutral: 'bg-layout-sun-100 dark:bg-layout-night-100'
      },
    };
  },
  watch: {
  'form.search': throttle(function () {
    this.$inertia.get(
      this.route('home.images.gallery', {
        slug: this.ocont.slug,
    }),
      { search: this.form.search },
      {
        preserveState: true,
        replace: true,
        skipLoading:true,
      }
    );
  }, 300),


//   'form.search': throttle(function (val) {
//     this.$inertia.get(
//       this.route('home.images.gallery'),
//       {
//         slug: this.ocont.slug,
//         search: val?.trim() || null,
//       },
//       {
//         preserveState: true,
//         replace: true,
//       }
//     );
//   }, 500),





      entries: {
    deep: true,
    immediate: true,
    handler() {
      if (!window.location.hash) return;

      requestAnimationFrame(() => {
        this.scrollToHashAnchor();
      });
    },
  },
},
  methods: {
     statusColor(status) {
      // Gibt die Tailwind-Klassen zurück oder Default
      return this.statusColorsMap[status] || 'bg-layout-sun-100 dark:bg-layout-night-100';
    },
    getStatus(str)
  {
    if(str == 'lost')
    {
        return "Verloren";
    }
    if(str == "sold")
    {
        return "Verkauft";
    }
    if(str == "givenaway")
    {
        return "Verschenkt";
    }
    return "";
  },
    CleanTable,
    //     scrollToHashAnchor() {
    //   const hash = window.location.hash;
    //   if (!hash) return;

    //   const el = document.getElementById(hash.replace("#", ""));
    //   if (!el) return;

    //   const y = el.getBoundingClientRect().top + window.scrollY - 134;
    //   window.scrollTo({ top: y, behavior: "smooth" });
    // },


  getHashElement() {
    const hash = window.location.hash;
//     console.log('DEBUG: window.location.hash =', hash);

    if (!hash) return null;

    // erlaubt: #st123 ODER #123
    const raw = hash.replace('#', '');
    const el = document.getElementById(raw) || document.getElementById(`st${raw}`);
//     console.log('DEBUG: target element =', el);
    return el;
  },

    scrollToHashAnchor() {
    const el = this.getHashElement();
    if (!el) return;

//     console.log('DEBUG: scrolling to element', el);

    const scroll = () => {
        const y = el.getBoundingClientRect().top + window.pageYOffset - 134;
        window.scrollTo({ top: y, behavior: 'smooth' });
//         console.log('DEBUG: scrolling to y =', y);
    };

    // Prüfe, ob Bilder noch laden
    const imgs = el.closest('#gallery')?.querySelectorAll('img');
    if (!imgs || imgs.length === 0) {
        scroll();
        return;
    }

    let loaded = 0;
    imgs.forEach((img) => {
        if (img.complete) loaded++;
        else img.addEventListener('load', () => {
        loaded++;
        if (loaded === imgs.length) scroll();
        });
    });

    // Falls alle Bilder schon geladen
    if (loaded === imgs.length) scroll();
    },





     // OLDDDDDDDDDDDDD
    // scrollToHashAnchor() {
    //   const hash = window.location.hash;
    //   if (hash && hash.startsWith("#")) {
    //     setTimeout(() => {
    //       const el = document.getElementById(hash.substring(1));
    //       if (el) {
    //         const y = el.getBoundingClientRect().top + window.pageYOffset - 134;
    //         window.scrollTo({ top: y, behavior: "smooth" });
    //       }
    //     }, 50);
    //   }
    // },
    reset() {
        this.form = mapValues(this.form, () => null);
    },
    stripTagsCom(txt)
    {
        txt = stripTags(txt,"br,i");
        return txt.replace(/(<br\s*\/?>\s*){2,}/gi, '<br>');
    },
    decodeEntities(text) {
      if (text) {
        text = text.replace(/<br\s*\/?>/g, "\n");
        return he.decode(text);
      }
      return "";
    },
    handleBodyClick() {
      // Hier evtl. Kommentare schließen o.Ä.
    },
    /**
     * Prüft, ob der Link ein Admin/Tables-Link ist.
     * @param {string} href
     * @returns {boolean}
     */
    isAdminLink(href) {
      return href.startsWith("/admin/tables");
    },
    // reset() {
    //   this.form.search = "";
    // },
  },
  mounted() {


    console.log("Breadcrumbs im Child:", this.breadcrumbs)

    this.$nextTick(() => {
    this.$refs.searchField?.focus();
    });
    // const hash = window.location.hash;
    // if (hash && hash.startsWith("#st")) {
    //   const id = hash.replace("#st", "");
    //   const index = this.entries.data.findIndex((item) => String(item?.id) === id);

    //   if (index !== -1) {
    //     this.openIndex = index;

    //     this.$nextTick(() => {
    //       const el = document.getElementById(`st${id}`);
    //       if (el) {
    //         const y = el.getBoundingClientRect().top + window.pageYOffset - 134;
    //         window.scrollTo({ top: y, behavior: 'smooth' });
    //       }
    //     });
    //   }
    // }

    this.lightbox = new PhotoSwipeLightbox({
      gallery: "#gallery",
      children: "a:not([href^='/admin/tables'])", // Admin-Links ausschließen
      pswpModule: () => import("photoswipe"),
      zoom: true,
      secondaryZoomLevel: 2,
      maxZoomLevel: 4,
      initialZoomLevel: "fit",
      wheelToZoom: true,
      barsSize: { top: 50, bottom: 50 },
      padding: { top: 30, bottom: 30, left: 30, right: 30 },
      showHideAnimationType: "zoom",
      galleryUID: "photoswipe-gallery",
    });

    this.lightbox.init();
    // this.scrollToHashAnchor();
},

};
</script>


  <style scoped>
  .gallery-container {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .imgprev {
    cursor: zoom-in;
    max-width: 100%;
    height: auto;
    border-radius: 8px;
  }
  </style>

