<template>
    <div class="bg-layout-sun-0 dark:bg-layout-night-0">

        <component :is="currentLayout">
    <MetaHeader title="Zugriff nicht möglich" />
        <div>
        <img :src="'/images/web/mcsl_logo_only.png'" class="mx-auto" style="max-width:500px;min-width:300px;width:100%" />
        <div class="mx-auto text-center" style="max-width: 800px;">
        <br />
        <br />
            <h2 class="text-4xl">{{texts.headline}}</h2>
        <br />
        <br />
            <p v-html="texts.text"></p>
    </div>
    </div>
    </component>
    </div>
</template>
<script>
import { SD } from "@/helpers";
import { markRaw } from "vue";

import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

import AbLayout from "@/Application/Homepage/Shared/ab/Layout.vue";
import MfxLayout from "@/Application/Homepage/Shared/mfx/Layout.vue";
import DagLayout from "@/Application/Homepage/Shared/dag/Layout.vue";
import PnaLayout from "@/Application/Homepage/Shared/pna/Layout.vue";
import ChhLayout from "@/Application/Homepage/Shared/chh/Layout.vue";

const layouts = {
    ab: AbLayout,
    mfx: MfxLayout,
    dag: DagLayout,
    pna: PnaLayout,
    chh: ChhLayout,
};

export default {
    components: {
        MetaHeader
    },

    props: {
        texts: {
            type: [Array, Object],
            required: true,
        }
    },

    data() {
        return {
            currentLayout: AbLayout
        };
    },

    mounted() {
        const subdomain = SD();

        this.currentLayout = markRaw(
            layouts[subdomain] || AbLayout
        );
    }
};
</script>


