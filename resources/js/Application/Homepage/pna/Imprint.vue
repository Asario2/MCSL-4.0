<template>
    <layout header-title="Impressum">
    <MetaHeader title="Impressum" />
        <page-content>
            <template #content>
                <markdown :markdown="rumLaut(imprint)"></markdown>
            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/pna/Layout.vue";
import { rumLaut } from "@/helpers";
import PageContent from "@/Application/Components/Content/PageContent.vue";
import Markdown from "@/Application/Components/Content/Markdown.vue";

export default defineComponent({
    name: "Homepage_Imprint_pna",

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
    methods:{
        rumLaut,
    }
});
</script>

