<template>
  <Layout>
    <MetaHeader title="Datenschutzerklärung" />

    <div
      class="bg-layout-sun-100 dark:bg-layout-night-100 p-7 overflow-auto"
    >
      <div ref="content" v-html="ch(processedHtml)"></div>

      <!-- ContactCard wird versteckt gerendert und später eingefügt pna -->
      <ContactCard
        v-if="vcardData"
        ref="vcard"
        :data="vcardData"
        style="display:none"
      />

    </div>
  </Layout>
</template>

<script>
import Layout from "@/Application/Homepage/Shared/pna/Layout.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import ContactCard from "@/Components/vcard.vue";

export default {

    name: "PrivacyPage",

    components: {
        Layout,
        MetaHeader,
        ContactCard
    },

    props: {
        privacy: String,
        vcardData: Object
    },

    data() {
        return {
            summarum: 134
        };
    },

    computed: {

        processedHtml() {

            return this.privacy.replace(
                "{{ vcard }}",
                "<vcard-placeholder></vcard-placeholder>"
            );
        }
    },

    mounted() {

        /*
         * 1. Warten bis das HTML aus v-html gerendert wurde
         */
        this.$nextTick(() => {

            /*
             * 2. Logo-Höhe ermitteln
             */
            const logo = document.getElementById("pna_logo");

            if (logo) {

                const height = logo.getBoundingClientRect().height;

                if (height > 160) {

                    alert("big");

                    this.summarum = 250;

                } else {

                    alert("sm");

                    this.summarum = 85;
                }
            }


            /*
             * 3. vCard an der Stelle des Placeholders einsetzen
             */
            const placeholder =
                this.$refs.content.querySelector("vcard-placeholder");

            if (placeholder && this.$refs.vcard) {

                placeholder.replaceWith(this.$refs.vcard.$el);

                this.$refs.vcard.$el.style.display = "";
            }


            /*
             * 4. Noch einmal auf den nächsten Vue-Render warten.
             *
             * Jetzt ist die vCard ebenfalls vollständig im DOM
             * und die endgültige Seitenhöhe steht fest.
             */
            this.$nextTick(() => {

                this.scrollToHashAnchor();

            });

        });


        /*
         * 5. Wenn sich der Hash später ändert,
         * ebenfalls zum entsprechenden Anker scrollen.
         */
        window.addEventListener(
            "hashchange",
            this.scrollToHashAnchor
        );
    },

    beforeUnmount() {

        window.removeEventListener(
            "hashchange",
            this.scrollToHashAnchor
        );
    },

    methods: {

        /*
         * HTML aufbereiten
         */
        ch(txt) {

            return txt
                .replace(/\n<li>/g, "<li>")
                .replace(/\n/g, "<br />");
        },


        /*
         * Zum Hash-Anker scrollen
         */
        scrollToHashAnchor() {

            this.$nextTick(() => {

                const hash = window.location.hash;

                if (!hash || !hash.startsWith("#")) {
                    return;
                }


                /*
                 * Kleiner Delay, damit Browser/Layout
                 * wirklich fertig berechnet sind.
                 */
                setTimeout(() => {

                    const id = decodeURIComponent(
                        hash.substring(1)
                    );

                    const el =
                        document.getElementById(id);

                    if (!el) {
                        return;
                    }


                    /*
                     * Position des Elements berechnen
                     *
                     * summarum = Höhe des Headers/Logos,
                     * damit der Anker nicht unter dem Header landet.
                     */
                    const y =
                        el.getBoundingClientRect().top +
                        window.pageYOffset -
                        this.summarum;


                    window.scrollTo({
                        top: y,
                        behavior: "smooth"
                    });

                }, 50);

            });
        }

    }
};
</script>
