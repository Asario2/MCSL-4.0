```vue
<template>
    <button
        type="button"
        @click="changeMode"
        class="flex items-center flex-nowrap cursor-pointer rounded-md px-4 py-2 border border-transparent leading-4 font-medium bg-transparent transition focus:outline-none"
    >
        <span v-if="mode === 'light'">
            <IconNight class="w-4 h-4" />
        </span>

        <span v-else>
            <IconSun class="w-4 h-4" />
        </span>
    </button>
</template>

<script>
import { defineComponent } from "vue";

import IconNight from "@/Application/Components/Icons/Night.vue";
import IconSun from "@/Application/Components/Icons/Sun.vue";

export default defineComponent({

    name: "ButtonChangeMode",

    components: {
        IconNight,
        IconSun
    },

    props: {

        mode: {
            type: String,

        },

        class: {

            type: String,

            default: `
                cursor-pointer
                inline-block
                rounded-lg
                px-2
                py-1
                text-sm
                text-layout-sun-700
                hover:bg-layout-sun-100
                hover:text-layout-sun-900
                dark:text-layout-night-700
                dark:hover:bg-layout-night-100
                dark:hover:text-layout-night-900
            `,
        },
    },

    emits: ["changeMode"],

    setup(props, { emit }) {

        function changeMode() {

            const newMode =
                props.mode === "dark"
                    ? "light"
                    : "dark";

            /*
            |--------------------------------------------------------------------------
            | LocalStorage speichern
            |--------------------------------------------------------------------------
            */

            localStorage.setItem(
                "theme",
                newMode
            );

            /*
            |--------------------------------------------------------------------------
            | Parent informieren
            |--------------------------------------------------------------------------
            */

            emit(
                "changeMode",
                newMode
            );

            /*
            |--------------------------------------------------------------------------
            | Dark Mode auf Server speichern
            |--------------------------------------------------------------------------
            */

            fetch("/toggle-dark-mode", {

                method: "POST",

                headers: {

                    "X-CSRF-TOKEN":
                        document.getElementById("token").value,

                    "Content-Type":
                        "application/json",
                },

                body: JSON.stringify({
                    dark_mode: newMode
                }),

            }).catch((error) => {

                console.error(
                    "Darkmode konnte nicht gespeichert werden:",
                    error
                );
            });

            /*
            |--------------------------------------------------------------------------
            | AI Buttons aktualisieren
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll(".ai-button")
                .forEach((aibut) => {

                    aibut.src =
                        `/images/icons/ai-${newMode}.png`;
                });

            document
                .querySelectorAll("#ai-image")
                .forEach((aiim) => {

                    aiim.src =
                        `/images/_ab/ai-teaser-${newMode}.jpg`;
                });

            /*
            |--------------------------------------------------------------------------
            | QR Code neu laden
            |--------------------------------------------------------------------------
            */

            reloadQrCode();
        }

        function reloadQrCode() {

            fetch(location.href)

                .then((response) => response.text())

                .then((html) => {

                    const parser =
                        new DOMParser();

                    const doc =
                        parser.parseFromString(
                            html,
                            "text/html"
                        );

                    const newSvg =
                        doc.querySelector(
                            "svg#QrCode"
                        );

                    const currentSvg =
                        document.querySelector(
                            "svg#QrCode"
                        );

                    if (
                        newSvg
                        && currentSvg
                    ) {

                        currentSvg.innerHTML =
                            newSvg.innerHTML;
                    }
                })

                .catch((error) => {

                    console.error(
                        "Error reloading QR Code:",
                        error
                    );
                });
        }

        return {
            changeMode
        };
    },
});
</script>
```
