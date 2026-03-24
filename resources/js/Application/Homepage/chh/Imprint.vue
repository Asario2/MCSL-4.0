<template>
    <layout header-title="Impressum">
    <MetaHeader title="Impressum" />
        <page-content>

            <template #content>
                <h1>Impressum</h1>
                <markdown :markdown="nl2br(rumLaut(imprint))"></markdown>
            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/chh/Layout.vue";
import {nl2br,rumLaut} from "@/helpers"
import PageContent from "@/Application/Components/Content/PageContent.vue";
import Markdown from "@/Application/Components/Content/Markdown.vue";

export default defineComponent({
    name: "Homepage_Imprint",

    props: {
        imprint: {
            type: String,
            required: true,
        },
    },

    components: {
        Layout,
        PageContent,
        Markdown,
        MetaHeader
    },
    mounted(){
        console.log(this.imprint);
    },
    methods:
    {
        nl2br,
        rumLaut,
    }
});
</script>
<style>
h5,h3,h2,h1,h4,h6{
    color:#002D62 !important;
}
ADDRESS{
    font-style:normal !important;
}
</style>
