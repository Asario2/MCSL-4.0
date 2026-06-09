<template>
    <Layout>
    <MetaHeader title="Datenschutzerklärung" />

    </Layout>
  </template>

  <script>
  import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  import { CleanTable } from '@/helpers';
  export default {
    components: {
    Layout, MetaHeader},

    mounted() {
        location.href='/home/infos/show/43';
        let asd = this.sadasd ;
        alert(asd);
        },

  methods: {

}
};
  </script>

