    <template>
        <component
            :is="layoutComponent"
            header-title="Blog"
            :header-url="$page.props.saas_url + '/blogs'"
            :header-image="$page.props.saas_url + '/images/blogimages/Blog_Idee_480x360.jpg'"
        >
        <MetaHeader title="Künstliche Inteligenz" />

            <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800">
                <div class="w-full max-w-7xl mx-auto mt-5">

                    <div v-if="data?.length">

                        <div
                            v-for="(item, index) in data"
                            :key="index"
                            class="group hover:no-underline focus:no-underline lg:grid lg:grid-cols-12 lg:gap-6 bg-layout-sun-100 dark:bg-layout-night-100 mb-6"
                        >

                            <!-- Bild -->
                            <div class="relative lg:col-span-7">

                                <div class="relative inline-block w-full">

                                    <img
                                        :src="impath"
                                        :alt="`Bild von ${item.headline}`"
                                        :class="imclass"
                                    >

                                    <div class="absolute bottom-0 right-0">
                                        <AiButton
                                            :nohome="nohomee"
                                            :dma="dmaa"
                                        />
                                    </div>

                                </div>

                            </div>

                            <!-- Inhalt -->
                            <div class="p-6 space-y-2 lg:col-span-5">

                                <div class="flex justify-end">

                                    <div
                                        v-if="item.author_name2"
                                        class="text-sm bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg"
                                    >
                                        {{ item.author_name2 }}
                                    </div>

                                </div>

                                <h2 class="text-xl font-semibold sm:text-2xl font-title group-hover:underline">
                                    {{ item.headline }}
                                </h2>

                                <div class="text-xs text-layout-sun-600 dark:text-layout-night-600">
                                    Von: {{ item.author_name }}
                                </div>

                                <span
                                    v-if="item?.text"
                                    v-html="item.text"
                                />

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
    import {
        defineComponent,
        defineAsyncComponent,
        markRaw
    } from "vue";

    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import PageTitle from "@/Application/Components/Content/PageTitle.vue";
    import AiButton from "@/Application/Components/Content/AiButton.vue";
    import BlogPreviewBig from "@/Application/Homepage/Shared/BlogPreviewBig.vue";
    import BlogPreviewSmall from "@/Application/Homepage/Shared/BlogPreviewSmall.vue";
    import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
    import Alert from "@/Application/Components/Content/Alert.vue";

    import { SD, GetRights } from "@/helpers";

    import {
        hasRight,
        loadAllRights,
        // isRightsReady
    } from "@/utils/rights";

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

            Markdown: defineAsyncComponent(() =>
                import("vue3-markdown-it")
            ),
        },

        props: {
            data: {
                type: Array,
                default: () => [],
            },

            blogarticle: {
                type: String,
                default: "",
            },
        },

        data() {
            return {
                rightsData: {},
                rightsReady: false,

                layoutComponent: null,

                darkMode: null,

                dmaa: null,
            };
        },

        computed: {

            impath() {
                return "/images/_" + this.SD() + "/ai-teaser-light.jpg";
            },

            imclass() {

                if (this.SD() === "ab") {
                    return "";
                }

                return "object-cover w-full rounded bg-layout-sun-500 dark:bg-layout-night-500 ai-image-corner";
            },

            nohomee() {
                return this.SD() !== "ab";
            },

            isRightsReadyComputed() {
                return this.rightsReady;
            },

        },

        methods: {

            SD,

            hasRight,

            async loadLayout() {

                const layouts = {

                    ab: () =>
                        import("@/Application/Homepage/Shared/ab/Layout.vue"),

                    dag: () =>
                        import("@/Application/Homepage/Shared/dag/Layout.vue"),

                    pna: () =>
                        import("@/Application/Homepage/Shared/pna/Layout.vue"),

                    mfx: () =>
                        import("@/Application/Homepage/Shared/mfx/Layout.vue"),

                    default: () =>
                        import("@/Application/Homepage/Shared/ab/Layout.vue"),
                };

                const layoutName = this.SD();

                const loader =
                    layouts[layoutName] || layouts.default;

                const layout = await loader();

                this.layoutComponent = markRaw(layout.default);
            },

            async checkRight(right, table) {

                const cacheKey = `${right}_${table}`;

                if (this.rightsData[cacheKey] !== undefined) {
                    return this.rightsData[cacheKey];
                }

                try {

                    const value = await GetRights(right, table);

                    this.rightsData[cacheKey] = value ?? 0;

                    return this.rightsData[cacheKey];

                } catch (e) {

                    console.error(
                        `Fehler bei checkRight(${right}, ${table})`,
                        e
                    );

                    this.rightsData[cacheKey] = 0;

                    return 0;
                }
            },

            async hasRightMethod(right, table) {

                const cacheKey = `${right}_${table}`;

                if (this.rightsData[cacheKey] === undefined) {
                    await this.checkRight(right, table);
                }

                return this.rightsData[cacheKey] === 1;
            },

        },

        async created() {

            await this.loadLayout();

        },

        async mounted() {

            try {

                await loadAllRights();

                this.rightsReady = true;

            } catch (e) {

                console.error(
                    "Fehler beim Laden der Rechte:",
                    e
                );
            }

            // localStorage nur im Browser
            if (typeof window !== "undefined") {
                this.darkMode = localStorage.getItem("theme");
            }

        },

    });
    </script>

    <style>
    .ai-image-corner {
        border-bottom-right-radius: 64px;
    }
    </style>
