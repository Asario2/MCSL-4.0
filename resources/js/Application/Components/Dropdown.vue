<template>
    <div
        class="relative z-50 h-full w-full lg:w-auto"
    >

        <!-- Trigger -->
        <div
            class="flex w-full justify-center lg:justify-end cursor-pointer"
            @click.stop="toggleDropdown"
        >
            <slot name="trigger"></slot>
        </div>

        <!-- Overlay -->
        <div
            v-if="open"
            class="fixed inset-0 z-40"
            @click="closeDropdown"
        ></div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >

            <div
                v-if="open"
                class="
                    absolute z-50 mt-2
                    left-0 right-0 w-full
                    rounded-md shadow-lg p-0.5
                    bg-layout-sun-0 dark:bg-layout-night-0
                    lg:left-auto lg:right-0 lg:w-72
                "
                @click.stop
            >
                <div
                    class="relative w-full rounded-md ring-1 ring-black ring-opacity-5 p-0 m-0"
                    :class="contentClasses"
                >
                    <slot name="content"></slot>
                </div>
            </div>

        </transition>

    </div>
</template>

<script>

export default {
    name: "Components_Content_Dropdown",

    props: {
        align: {
            type: String,
            default: "right",
        },

        width: {
            type: String,
            default: "96",
        },

        autoClose: {
            type: Boolean,
            default: true,
        },

        contentClasses: {
            type: Array,
            default: function () {
                return [
                    "px-4",
                    "py-2",
                    "text-xs",
                    "bg-layout-sun-50",
                    "text-layout-sun-900",
                    "dark:bg-layout-night-50",
                    "dark:text-layout-night-900",
                ];
            },
        },
    },

    data() {
        return {
            open: false,
        };
    },

    methods: {
        toggleDropdown() {
            this.open = !this.open;
        },

        closeDropdown() {
            if (this.autoClose) {
                this.open = false;
            }
        },

        closeOnEscape(e) {
            if (this.open && e.key === "Escape") {
                this.closeDropdown();
            }
        },
    },

    mounted() {
        document.addEventListener(
            "keydown",
            this.closeOnEscape
        );
    },

    beforeUnmount() {
        document.removeEventListener(
            "keydown",
            this.closeOnEscape
        );
    },
};
</script>
