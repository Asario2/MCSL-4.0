<template>
    <Layout>
        <MetaHeader title="Rechtsanwalt Christian Henning" />
        <div>
<div class="flex flex-wrap">
<div class="w-full md:w-8/12 lg:w-8/12 pr-10">
<article>
<h1 class='bl'>{{data.headline}}</h1>
<p class='det' v-html="nl2br(data.details)"></p>
</article>
</div>
<div class="w-full md:w-4/12 lg:w-4/12 pr-10">
<article>
<h1 class='bl'>{{datb.headline}}</h1>
<p class='det' v-html="nl2br(datb.details)"></p>
</article>
</div>
</div>
</div>



    </Layout>
</template>
<script>
import Layout from '@/Application/Homepage/Shared/chh/Layout.vue';
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import {nl2br} from "@/helpers";
export default {
    name: 'NewHome',
    components: { Layout, MetaHeader },
    props:{
        data:[Object,Array],
        datb:[Object,Array],
    },
    methods: {
        nl2br,

    // andere Methoden hier...
  }
};
</script>
