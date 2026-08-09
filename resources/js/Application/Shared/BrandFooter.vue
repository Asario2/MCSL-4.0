<template>
    <div v-if="sd == 'ab'">
<Link :href="routeName" class="flex items-center nul">
            <div>
                <favicon class="h-10 w-10 mr-4"></favicon>
            </div>
            <div
                class="hidden lg:block flex-col items-start justify-center md:flex"
            >
                <div
                    class="font-logo text-xl lg:text-3xl leading-snug whitespace-nowrap tracking-widest font-bold"
                >
                    <span
                        v-if="brand_1"
                        class="text-primary-sun-500 dark:text-primary-night-500 glow-blue euros text-2xl"
                        >{{ brand_1 }}</span
                    >
                    <span
                        v-if="brand_2"
                        class="text-layout-sun-500 dark:text-layout-night-500 glow-blue-gray euros text-2xl"
                        >{{ brand_2 }}</span
                    >
                </div>
                <div
                    class="font-title text-xs lg:text-sm mt-[5px] ml-[59px] leading-snug whitespace-nowrap tracking-wide text-layout-sun-600 dark:text-layout-night-900 euros"
                    v-if="appName"
                >
                    <span v-html="appName"></span>
                </div>
            </div>
        </Link>
    </div>
    <div v-if="SD() == 'mfx'" >
        <a href="/"><mfxlogo :ab="'_mfx_alt' + GetLogin()"></mfxlogo></a>
    </div>
    <div v-if="SD() == 'pna'" class="dark:bg-layout-night-0 min-w-[100%]">
        <a href="/"><pnalogo :small="true" :ab="'_pna_alt' + GetLogin()"></pnalogo></a>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import {SD} from "@/helpers";
import Favicon from "@/Application/Components/Logo/Favicon.vue";
import mfxlogo from "@/Application/Shared/mfxlogo.vue";
import pnalogo from "@/Application/Shared/pnalogo.vue";
export default {
    name: "Shared_BrandFooter",

    components: {
        Link,
        Favicon,
        mfxlogo,
        pnalogo,
    },

    data() {
        return {
            sd: null,
            loginFlag: "",
        };
    },

    mounted() {
        if (typeof window !== "undefined") {
            this.sd = window.subdomain;
            this.loginFlag = this.calcLogin();
        }
    },

    props: {
        routeName: {
            type: String,
            default: "home",
        },
        brand_1: {
            type: String,
            default: "Asarios",
        },
        brand_2: {
            type: String,
            default: "Blog",
        },
        appName: {
            type: String,
            default: null,
        },
    },

    methods: {
        SD,
        calcLogin() {
            const url = window?.location?.href || "";

            if (
                !url.includes("/login") &&
                !url.includes("/forgot-password") &&
                !url.includes("/register") &&
                !url.includes("/email/verify") &&
                !url.includes("/reset-password") &&
                !url.includes("/confirm-password") &&
                !url.includes("/verify-email")
            ) {
                return "";
            }

            return "l";
        },
        GetLogin()
        {
             if (typeof window === "undefined") {
                return "l";
            }
            const url = location.href;
            if(!url.includes("/login") && !url.includes("/forgot-password") && !url.includes("/register") && !url.includes("/email/verify") && !url.includes("reset-password") && !url.includes("/confirm-password") && !url.includes("/verify-email"))
            {
                return "";
            }
            return "l";
        },

    },

};
</script>

