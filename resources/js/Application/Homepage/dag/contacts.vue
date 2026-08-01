<template>
    <layout :header-url="$page.props.saas_url + '/'">
        <MetaHeader title="Contacts" />

        <page-content>
            <template #content>
                <div>
                    <page-title>
                        <template #title>
                            <span class="dark:text-layout-night-1050 text-layout-sun-100">
                                {{ text?.headline }}
                            </span>
                            <editbtns id="16" table="texts"></editbtns>
                        </template>
                    </page-title>

                    <div class="w-full p-1 md:p-4 lg:rounded-lg">

                        <!-- Einleitung -->
                        <section
                            class="bg-layout-sun-100 dark:bg-layout-night-50 lg:rounded-lg p-2 mb-6 border border-layout-sun-1000 dark:border-layout-night-1050"
                        >
                            <div
                                v-if="text"
                                class="text-layout-sun-1000 dark:text-layout-night-1000"
                            >
                                <div v-html="text.text"></div>
                            </div>

                            <div v-else>
                                <p class="text-gray-500 italic">
                                    Kein Willkommenstext vorhanden.
                                </p>
                            </div>
                        </section>

                        <!-- Kontaktbereich -->
                        <section class="grid grid-cols-12 gap-6 w-full">

                            <!-- Linke Seite -->
                            <section
                                class="col-span-12 md:col-span-6
                                       bg-layout-sun-100 dark:bg-layout-night-50
                                       border border-layout-sun-1000 dark:border-layout-night-1050
                                       rounded-lg p-4"
                            >
                                <h2
                                    v-html="contacts.headline + 'sad'"
                                    class="text-xl font-semibold mb-2 mt-0"
                                ></h2>

                                <p
                                    v-html="rumLaut(contacts.text)"
                                    class="text-layout-sun-900 dark:text-layout-night-900"
                                ></p>

                                <div class="relative">
                                    <img
                                        :src="'/images/_mfx/web/contacts_ai.jpg'"
                                        class="mt-[12px] ai-image-corner"
                                    />

                                    <AiButton :dma="dmaa"></AiButton>
                                </div>
                            </section>

                            <!-- Rechte Seite -->
                            <section
                                class="col-span-12 md:col-span-6
                                       bg-layout-sun-100 dark:bg-layout-night-50

                                       rounded-lg p-4"
                            >
                                <h2 class="text-xl font-semibold mb-4">
                                    Kontaktformular
                                </h2>

                                <emailview :nohead="true"></emailview>
                            </section>

                        </section>

                    </div>
                </div>
            </template>
        </page-content>
    </layout>
</template>
    <script>
    import { defineComponent } from "vue";
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import Layout from "@/Application/Homepage/Shared//Layout.vue";
    import AiButton from "@/Application/Components/Content/AiButton.vue";
    import {rumLaut } from "@/helpers";
    import editbtns from "@/Application/Components/Form/editbtns.vue";
    import PageContent from "@/Application/Components/Content/PageContent.vue";
    import PageTitle from "@/Application/Components/Content/PageTitle.vue";
    import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";
    import emailview from "@/Application/Components/Form/email.vue";
    import axios from "axios";
    export default defineComponent({
        name: "Homepage_Home",

        components: {
            Layout,
            PageContent,
            PageTitle,
            PageParagraph,
            emailview,
            editbtns,
            AiButton,
            MetaHeader},
        props:{
            news:[Array,Object],
            text: [Array,Object,String],
            contacts: [Array,Object,String],
            dmaa:[Array,Object,String,Number],
            dma:{
            type:[Array,Object,String,Number],
            default:null,
            }
        },
        data() {
        return {
        form: {
            name: '',
            email: '',
            subject: '',
            message: '',
            captcha: '',
            accepted: false
        }
        }
    },

        methods: {
    cleanHtml(html) {
        const result = rumLaut(html);
        const resu = stripTags(result);
        // console.log("rumLaut output:", result);
        return resu;

        },
        async submitForm() {
        try {
            const response = await axios.post('/contact/send', this.form)
            alert('Nachricht erfolgreich gesendet!')
            this.resetForm()
        } catch (error) {
            alert('Fehler beim Senden der Nachricht.')
        }
        },
        resetForm() {
        this.form = {
            name: '',
            email: '',
            subject: '',
            message: '',
            captcha: '',
            accepted: false
        }
        },
        rumLaut,

    },
    });
    </script>

