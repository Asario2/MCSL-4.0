    <template>
        <template v-if="routeName">
            <!-- <span v-if="isFirst">&nbsp;&nbsp;</span> -->
            <Link
            :href="routeName"
            class="text-layout-sun-600 dark:text-layout-night-900 cursor-pointer inline-block
                rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-layout-sun-200
                hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200
                 dark:hover:text-layout-night-1000 nul nul_bla dark:nul_wh whitespace-nowrap ml-[16px]"
            :preserve-state="false"
            :preserve-scroll="false"
            replace
            >

            <span>
                {{ name }}
            </span>
            </Link>
        </template>
        <template v-else>
            <div
                class="text-layout-sun-600 dark:text-layout-night-900 cursor-pointer inline-block
                rounded-lg px-0  py-0 text-sm text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900
                 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000 nul nul_bla dark:nul_wh whitespace-nowrap"
            >
                <slot></slot>
            </div>
        </template>
    </template>

    <script>
    //
    import { Link } from '@inertiajs/vue3'
    export default {
        name: "Shared_LinkHeader",

           components: {
                Link, // 👈 DAS fehlte
            },
        //
        props: {
            routeName: {
                type: String,
                default: "home",
            },
            name: {
                type: String,
                default: "MSCL",
            },
            imgsrc:{
                type:String,
                default:'',
            },
            ialt:{
                type:String,
                default:'',
            },
            isFirst: {
                type: Boolean,
            },
        },
        mounted() {

        }
    };
    </script>

    <style>
    /* .tw{
        color:#fff !important;
    }
    A.text-layout-sun-600 dark:text-layout-night-900{
        color:rgb(20,20,20) !important;
    } */
     .nul_bla{color:#000 !important}
    </style>

