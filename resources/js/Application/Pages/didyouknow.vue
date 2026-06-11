<template>
  <Layout>
    <MetaHeader title="DidYouKnow" />

    <section class="max-w-3xl mx-auto mt-10 px-4">
      <h1 class="text-3xl font-bold mb-6 text-layout-title">DidYouKnow - Wussten Sie schon?</h1>

      <newbtn table="didyouknow" />

      <div class="flex justify-between items-center">
        <search-filter
          v-if="searchFilter"
          v-model="form.search"
          :placeholder="searchText"
          class="w-full"
          @reset="reset"
        />
      </div>

      <div
        class="p-2 md:p-4"
        v-if="Array.isArray(items.data) && items.data.length === 0 && form.search"
      >
        <alert type="warning">
          Für den vorgegebenen Suchbegriff wurden keine DidYouKnows gefunden.
        </alert>
      </div>

      <article
        v-for="(item, index) in items.data"
        :key="item.id || index"
        class="mb-4 border border-gray-300 dark:border-gray-600 rounded-lg"
      >
        <div :id="'st' + item.id"></div>

        <button
          @click="toggle(index, item)"
          class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between items-center transition"
        >
          <span class="text-lg font-medium text-gray-900 dark:text-white">
            Wussten Sie schon, {{ item.headline }}
          </span>
          <svg
            :class="{ 'rotate-180': openIndex === index }"
            class="h-5 w-5 transform transition-transform text-gray-600 dark:text-gray-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div
          v-if="openIndex === index || items.meta.total == '1'"
          :ref="`content-${item.id}`"
          class="px-4 py-3 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-300 border-t border-gray-300 dark:border-gray-700"
        >
          <p v-html="item.answer || 'Kein Text vorhanden.'">
          </p>
          <editbtns :id="item.id" table="didyouknow" /><br />
          <SocialButtons :name="item?.headline" :postId="item.id"  :title="'Wussten Sie schon, '+item.headline" :xslug="true" :nostars="true" :sse="item.headline"/>
        </div>
      </article>
    </section>

    <Pagination :links="items.links" basePath="home/didyouknow" />
  </Layout>
</template>

<script>
import Layout from '@/Application/Homepage/Shared/Layout.vue';
import MetaHeader from '@/Application/Homepage/Shared/MetaHeader.vue';
import newbtn from '@/Application/Components/Form/newbtn.vue';
import SearchFilter from '@/Application/Components/Lists/SearchFilter.vue';
import Alert from '@/Application/Components/Content/Alert.vue';
import {stripTags} from "@/helpers";
import editbtns from '@/Application/Components/Form/editbtns.vue';
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
// import RatingWrapper from "@/Application/Components/Social/RatingWrapper.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import Pagination from "@/Application/Components/Pagination.vue";

export default {
  name:"DidYouKnow",
  components: { Pagination, Layout, MetaHeader, newbtn, SearchFilter, Alert, editbtns, SocialButtons },
  props: {
    items: { type: Object, required: true },
    ratings: { type: [Array, Object], default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    searchFilter: { type: Boolean, default: true },
    searchText: { type: String, default: "Hier kannst du den Suchbegriff eingeben" },
  },
  data() {
    return {
      openIndex: null,
      form: { search: this.filters?.search ?? "" },
    };
  },
  watch: {
    form: {
      handler: throttle(function () {
        const query = pickBy(this.form, v => v != null && v !== '');
        this.$inertia.get(
          this.route("home.didyouknow"),
          Object.keys(query).length ? query : { remember: "forget" },
          { preserveState: true, preserveScroll: false, replace: true, skipLoading:true }
        );
      }, 150),
      deep: true
    },
    items: {
      immediate: true,
      async handler() {
        await this.$nextTick();
        this.scrollToHash();
      }
    }
  },
  methods: {
    stripTags,
    reset() { this.form.search = null },

    toggle(index, item) {
      const opening = this.openIndex !== index;
      this.openIndex = opening ? index : null;
      if (opening && item?.id) this.$nextTick(() => this.scrollToItem(item.id));
    },

    async scrollToHash() {
  // 👉 SSR Guard ganz am Anfang
  if (typeof window === 'undefined') return;

  const hash = window.location.hash;
  if (!hash) return;

  const id = hash.replace('#st', '');
  const index = this.items?.data?.findIndex(item => String(item.id) === id);
  if (index === -1) return;

  this.openIndex = index;

  await this.$nextTick();
  await new Promise(r => requestAnimationFrame(r));
  await new Promise(r => requestAnimationFrame(r));

  const marker = document.getElementById(`st${id}`);
  if (!marker) return;

  // 👉 Funktion außerhalb definieren
  const doScroll = () => {
    const y = marker.getBoundingClientRect().top + window.pageYOffset - 180;
    window.scrollTo({ top: y, behavior: 'auto' });
  };

  const content = this.$refs[`content-${id}`]?.[0];
  const imgs = content ? content.querySelectorAll('img') : [];

  if (!imgs.length) {
    doScroll();
    return;
  }

  let loaded = 0;

  const check = () => {
    loaded++;
    if (loaded === imgs.length) doScroll();
  };

  imgs.forEach(img => {
    if (img.complete) {
      check();
    } else {
      img.addEventListener('load', check, { once: true });
      img.addEventListener('error', check, { once: true }); // 👈 wichtig!
    }
  });

  // Falls alle schon geladen sind
  if ([...imgs].every(img => img.complete)) {
    doScroll();
  }
},

    scrollToItem(id) {
      const content = this.$refs[`content-${id}`]?.[0];
      if (!content) return;

      const doScroll = () => {
        const y = content.getBoundingClientRect().top + window.pageYOffset - 170;
        window.scrollTo({ top: y, behavior: 'smooth' });
      };

      const imgs = content.querySelectorAll('img');
      if (imgs.length === 0) return doScroll();

      let loaded = 0;
      imgs.forEach(img => {
        if (img.complete) loaded++;
        else img.addEventListener('load', () => {
          loaded++;
          if (loaded === imgs.length) doScroll();
        });
      });
      if (loaded === imgs.length) doScroll();
    }
  },
  mounted() {
    this.scrollToHash();
  },
  created() {
    if (typeof window === 'undefined') return;
    document.addEventListener('inertia:finish', () => {
        this.$nextTick(() => {
        this.scrollToHash();
        });
    });
    }
};
</script>
