<template>
<Layout>
    <MetaHeader :title="'GitHubDataBaseCreator'" />

        <template #header>
            <breadcrumb :breadcrumbs="data.breadcrumbs" />
        </template>
<h2 class="haeding">
    First Commit:
</h2>
    <span v-html="data.details.join('<br>')"></span>
    <br /><br />
<h2 class="subhead">Veränderungen an der DB</h2>
    <div v-for="(item, index) in data.nw" :key="index">
    <div>
        <strong>Domain:</strong> {{ item.original.dom }}
    </div>

    <div>
        <strong>Status:</strong> {{ item.original.message }}
    </div>

    <div>
        <strong>Datei:</strong> {{ item.original['Datei verändert'] }}
    </div>

    <div>
        <span v-if="item.original['Summe Änderung'] != 0 ">✅</span><span v-else>✔</span> <strong>Änderungen:</strong> {{ item.original['Summe Änderung'] }}
        <!-- {{ item }} -->
          <br />
        <span v-if="item.original['Summe Änderung'] != 0 "><b>Veränderungen:</b><br /><div class="whitespace-pre-line">
        <span class="code" v-html="GetQRYS(item.original['QueryString'])"></span>
        </div></span>
    </div>

    <hr class="my-4">
</div>
<h4>Letztes Update: {{ data.lastUpate }}</h4>
</Layout>
</template>

    <script>
    import { defineComponent } from "vue";
    import Layout from "@/Application/Admin/Shared/ab/Layout.vue"
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import PageTitle from "@/Application/Components/Content/PageTitle.vue";
    import AiButton from "@/Application/Components/Content/AiButton.vue";
    import BlogPreviewBig from "@/Application/Homepage/Shared/BlogPreviewBig.vue";
    import BlogPreviewSmall from "@/Application/Homepage/Shared/BlogPreviewSmall.vue";
    import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
    import Alert from "@/Application/Components/Content/Alert.vue";
    import Markdown from "@/Application/Components/Content/Markdown.vue";
    import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
    import mapValues from "lodash/mapValues";
    import pickBy from "lodash/pickBy";
    import throttle from "lodash/throttle";

    import { nl2br } from "@/helpers";
    import { hasRight, loadAllRights, isRightsReady } from '@/utils/rights';

    export default defineComponent({
    name: "Homepage_AiContent",

    components: {
        Layout,
        MetaHeader,
        Breadcrumb,
    },

    data() {
        return {

        };
    },

    props: {
        data: {
        type: Array,
        required: true,
        default: () => [],
        },
        breadcrumbs: {
        type: Array,
        required: true,
        default: () => [],
        },

    },
    methods:{
        nl2br,
        GetQRYS(str)
        {
            return this.nl2br(str);
        }
    },
    mounted(){
        if(this.data.status){
            window.toastBus.emit({type:this.data.status,message:this.data.message});
        }
    },
    computed: {

    }
});
</script>

    <style>
    SPAN.code{
        background-color:#ccc;
        color:#000;
    }
    </style>
