    <template>
    <component :is="layoutComponent"
                header-title="Blog"
                :header-url="$page.props.saas_url + '/blogs'"
                :header-image="$page.props.saas_url + '/images/blogimages/Blog_Idee_480x360.jpg'">

        <MetaHeader title="Künstliche Inteligenz" />

        <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800">
        <div class="w-full max-w-7xl mx-auto mt-5">
            <div v-if="data.length">
            <div v-for="(item, index) in data" :key="index"
                class="group hover:no-underline focus:no-underline lg:grid lg:grid-cols-12 lg:gap-6 bg-layout-sun-100 dark:bg-layout-night-100">

                <!-- Bild-Bereich -->
                <div class="relative lg:col-span-7">

                 <div class="relative inline-block">

                    <img
                    :src="impath"
                    :alt="`Bild von ${item.headline}`"
                    :class="imclass"
                    />

                    <div class="absolute bottom-0 right-0">
                    <AiButton :nohome="nohomee" :dma="dmaa" />
                    </div>

                </div>
                </div>

                <!-- Text-Bereich -->
                <div class="p-6 space-y-2 lg:col-span-5">
                <div class="flex justify-end">
                    <div v-if="item.author_name2"
                        class="text-sm bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg">
                    </div>
                </div>

                <h2 class="text-xl font-semibold sm:text-2xl font-title group-hover:underline">
                    {{ item.headline }}
                </h2>

                <div class="text-xs text-layout-sun-600 dark:text-layout-night-600">
                    Von: {{ item.author_name }}
                </div>

                <span v-html="item.text"></span>

                </div>
            </div>
            </div>
            <div v-else>
            <p>Keine Daten gefunden</p>
            </div>
        </div>
        </section>
    </component>
    </template>

    <script>
    import { defineComponent, defineAsyncComponent } from "vue";
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import PageTitle from "@/Application/Components/Content/PageTitle.vue";
    import AiButton from "@/Application/Components/Content/AiButton.vue";
    import BlogPreviewBig from "@/Application/Homepage/Shared/BlogPreviewBig.vue";
    import BlogPreviewSmall from "@/Application/Homepage/Shared/BlogPreviewSmall.vue";
    import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
    import Alert from "@/Application/Components/Content/Alert.vue";
    import Markdown from "@/Application/Components/Content/Markdown.vue";

    import mapValues from "lodash/mapValues";
    import pickBy from "lodash/pickBy";
    import throttle from "lodash/throttle";

    import { SD, GetRights } from "@/helpers";
    import { hasRight, loadAllRights, isRightsReady } from '@/utils/rights';

    export default defineComponent({
    name: "Homepage_AiContent",

    components: {
        MetaHeader,
        PageTitle,
        BlogPreviewBig,
        BlogPreviewSmall,
        SearchFilter,
        Alert,
        AiButton,
        Markdown: defineAsyncComponent(() => import("vue3-markdown-it")),
    },

    data() {
        return {
        rightsData: {},
        rightsReady: false,
        layoutComponent: null,
        darkMode: null,
        dmaa: null, // Beispiel prop für AiButton
        };
    },

    props: {
        data: {
        type: Array,
        required: true,
        default: () => [],
        },
        blogarticle: String,
    },

    computed: {
        impath() {
        return '/images/_' + SD() + '/ai-teaser-light.jpg';
        },
        imclass() {
        if (SD() === "ab") return "";
        return "object-cover w-full rounded bg-layout-sun-500 dark:bg-layout-night-500 ai-image-corner";
        },
        nohomee() {
        return SD() === "ab" ? false : true;
        },
        isRightsReady() {
        return this.$isRightsReady;
        },
        hasRight() {
        return this.$hasRight;
        },
    },

    methods: {
        SD,

        async loadLayout() {
        const layouts = {
            ab: () => import('@/Application/Homepage/Shared/ab/Layout.vue'),
            dag: () => import('@/Application/Homepage/Shared/dag/Layout.vue'),
            mfx: () => import('@/Application/Homepage/Shared/mfx/Layout.vue'),
            default: () => import('@/Application/Homepage/Shared/ab/Layout.vue'),
        };
        const layoutName = this.SD();
        const loader = layouts[layoutName] || layouts.default;
        const layout = await loader();
        this.layoutComponent = layout.default;
        },

        async checkRight(right, table) {
        const value = await GetRights(right, table);
        this.$set(this.rightsData, `${right}_${table}`, value);
        },

        async hasRightMethod(right, table) {
        if (!this.rightsData[`${right}_${table}`] && table) {
            await this.checkRight(right, table);
        }
        return this.rightsData[`${right}_${table}`] === 1;
        },
    },

    async created() {
        await this.loadLayout();
    },

    async mounted() {
        await loadAllRights();
        this.rightsReady = true;
        this.darkMode = localStorage.getItem("theme");
    },
    });
    </script>

    <style>
    .ai-image-corner {
    border-bottom-right-radius: 64px;
    }
    </style>
