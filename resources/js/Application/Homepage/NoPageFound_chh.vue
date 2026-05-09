<template>
  <component
    :is="Layout"
    :header-url="$page.props.saas_url + '/no_page_found'"
  >
    <MetaHeader title="Seite nicht gefunden" />

    <page-title>
      <template #title>Seite nicht gefunden!</template>
    </page-title>
  </component>
</template>

<script>
import { markRaw } from "vue";

import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";

const layoutComponents = {
  chh: () => import('@/Application/Homepage/Shared/chh/Layout.vue'),
  mfx: () => import('@/Application/Homepage/Shared/mfx/Layout.vue'),
  dag: () => import('@/Application/Homepage/Shared/dag/Layout.vue'),
  default: () => import('@/Application/Homepage/Shared/chh/Layout.vue'),
};

function getDomKey(hostname) {
  if (!hostname) return "default";
  const parts = hostname.split('.');
  return parts.length > 2 ? parts[0] : "default";
}

export default {

  name: "Homepage_NoPageFound_chh2",

  components: {
    MetaHeader,
    PageTitle
  },

  data() {
    return {
      Layout: null
    };
  },

  async created() {

    const subdomain =
      this.$page?.props?.subdomain || "default";

    const key = getDomKey(subdomain);

    const loader =
      layoutComponents[key] ||
      layoutComponents.default;

    const layout = await loader();

    this.Layout = markRaw(layout.default);
  }
};
</script>
