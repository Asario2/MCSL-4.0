<template>
    <span
        v-if="isClient"
        class="flex items-center justify-start w-full cursor-pointer px-4 py-2 text-sm leading-4 font-medium transition focus:outline-none hover:underline dark:bg-layout-night-50 text-layout-sun-700 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:text-layout-night-900"
        style="z-index:50;"
    >

        <!-- MIT ROUTE -->
        <template v-if="withRoute">

            <a
                :title="tippyText"
                :href="routeName"
                :target="withTarget ? '_blank' : '_self'"
                class="flex items-center justify-center w-full text-center"
            >
                <span class="flex items-center justify-center gap-1 w-full text-center">
                    <slot></slot>
                </span>
            </a>

        </template>


        <!-- OHNE ROUTE -->
        <template v-else>

            <span class="flex items-center justify-center gap-1 w-full text-center">
                <slot></slot>
            </span>

        </template>

    </span>
</template> 

<script>
import IconChevronDown from "@/Application/Components/Icons/ChevronDown.vue";

export default {

    name: "Components_Content_DropdownLink",

    components: {
        IconChevronDown,
    },

    data() {
        return {
            isClient: false,
        };
    },

    mounted() {
        this.isClient = true;
    },

    props: {
        routeName: {
            type: String,
            default: "",
        },

        withIcon: {
            type: Boolean,
            default: false,
        },

        withRoute: {
            type: Boolean,
            default: false,
        },

        withTarget: {
            type: Boolean,
            default: false,
        },

        tippyText: {
            type: String,
            default: null,
        },
    },
};
</script>