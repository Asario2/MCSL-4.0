<template>
    <layout header-title="Impressum">
    <MetaHeader title="Impressum" />
        <page-content>
            <template #content>
                <div class="bg-layout-sun-100 dark:bg-layout-night-100 p-7">
                <markdown :markdown="imprint"></markdown>
                </div>
            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/dag/Layout.vue";

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
});
</script>

