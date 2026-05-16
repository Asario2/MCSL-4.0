<!-- EmailSender.vue -->
<template>
  <Layout>
    <MetaHeader title="Unused Images" />

    <template #header>
      <Breadcrumb :breadcrumbs="breadcrumbs" />
    </template>

    <section
      class="block max-w-sm mx-auto sm:max-w-full p-4 bg-layout-sun-100 dark:bg-layout-night-100"
    >
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">

        <!-- LEFT -->
        <h1 class="text-3xl font-bold text-layout-title">
          Unused Images
        </h1>

        <!-- RIGHT -->
        <div class="flex items-center gap-2 ml-auto">
          <label class="text-sm font-medium text-layout-title">
            Domain
          </label>

          <select
            v-model="dom"
            @change="domres"
            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-layout-night-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="ab">Asario.de</option>
            <option value="mfx">MarbleFX</option>
            <option value="dag">Monikadargies.de</option>
            <option value="chh">ra-c-henning.de</option>
          </select>
        </div>

      </div>

      <photoswipe_old
        :dom="dom"
        :images="images_container"
        :basePath="images_container.basepath"
        :checkable="true"
      />

    </section>
  </Layout>
</template>

<script>
import photoswipe_old from "@/Application/Components/photoswipe_old.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import { SD } from "@/helpers";

export default {
  name: "OldGallery",

  components: {
    Layout,
    Breadcrumb,
    MetaHeader,
    photoswipe_old,
  },

  props: {
    images_container: Object,
    im_cont: Object,
  },

  data() {
      const parts = window.location.pathname.split('/');
  const last = parts[parts.length - 1];

  return {
    dom: last || SD(),
  };
  },

  methods: {
    SD,

    domres() {
      // optional alert entfernen wenn nicht nötig
      // alert(this.dom);

      location.href = "/admin/get_unused_imgz/" + this.dom;
    },
  },
};
</script>

<style>
button {
  outline: none;
}
.w-fully {
  min-width: 100% !important;
  max-width: 100% !important;
}
</style>
