<template>
    <Layout>
        <MetaHeader title="Rechtsanwalt Christian Henning" />
        <div>
            <h1>Auszug Veröffentlichungen / Presse</h1>
  <div class="flex justify-between items-center">
                <search-filter
                v-if="searchFilter"
                v-model="form.search"
                class="w-full"
                ref="searchField"
                @reset="reset"
                /></div>
<div class="w-full">

    <div
        v-for="(db) in filteredContacts"
        :key="db.pid"
        class="odd:bg-gray-100/40 even:bg-transparent hover:bg-gray-300/60 transition-colors p-1"
    >
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">

            <!-- LEFT CONTENT -->
            <div class="flex-1">
                <h2 class="font-semibold text-xl" v-html="rumLaut(db.name)"></h2>

                <span class="text-sm text-gray-600">
                    Veröffentlicht am {{ db.oeff }} in
                    <a class="quelle ml-1" :href="db.quellurl">
                        {{ rumLaut(db.qname) }}
                    </a>
                    - <b>Ausgabe:</b> {{ db.QuellenAusgabe || '-' }}
                    <b class="ml-2">Seite:</b> {{ db.QuellenSeite || '-' }}
                </span>
            </div>

            <!-- DOWNLOAD -->
            <div class="flex items-center">
                <a
                    class="inline-flex items-center gap-2 quelle"
                    :href="'files/_chh/publikationen/file_pdf/' + db.file_pdf"
                >
                    <img :src="'/images/icons/PDF.png'" class="h-8" />
                    <span>Download ({{ db.filesize }})</span>
                </a>
            </div>

            <!-- EDIT BUTTONS -->
            <div v-if="hasr" class="flex justify-start md:justify-end">
                <editbtns table="publikationen" :id="db.pid" />
            </div>

        </div>
    </div>




</div>
<Pagination :links="pag.links" basePath="publikationen" />
</div>




    </Layout>
</template>
<script>
import Layout from '@/Application/Homepage/Shared/chh/Layout.vue';
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import Pagination from "@/Application/Components/Pagination.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import {nl2br,rumLaut,GetRights} from "@/helpers";
import pickBy from "lodash/pickBy";
import { throttle } from "lodash";
export default {
    name: 'NewHome',
    components: { Layout, MetaHeader,editbtns, SearchFilter, Pagination},
    props:{
        data:[Object,Array],
        datb:[Object,Array],
        pag:[Object,Array],
        filters: { type: Object, default: () => ({}) },
        searchFilter: { type: Boolean, default: true },
        searchText: { type: String, default: "Hier kannst du den Suchbegriff eingeben" },
    },
    data() {
        return {

            form: {
                search: new URLSearchParams(window.location.search).get('search') || ''
            },
            open:false,
        }
    },
    methods: {
        nl2br,
        rumLaut,
        GetRights,
        reset() { this.form.search = null },
  },
  computed:
  {
    hasr(){

    return GetRights("edit","publikationen");
    },
    filteredContacts() {
        return this.data;
    },
},
watch: {
    form: {
      handler: throttle(function () {
        const query = pickBy(this.form, v => v != null && v !== '');
        this.$inertia.get(
        this.route("home.publication"),
        query,
        {
            preserveState: true,
            preserveScroll: false,
            replace: true,
            skipLoading: true,
        }
        );

      }, 150),
      deep: true
    },
}
    // filteredContacts() {
    //     if (!this.form.search) return this.data;

    //     const s = this.form.search.toLowerCase();

    //     return this.data.filter(c => {
    //         return [
    //             c.name,
    //             c.QuellenAusgabe,
    //             c.QuellenSeite,
    //             c.file_pdf,
    //             c.qname,
    //             c.QuellUrl
    //         ].some(val =>
    //             val && String(val).toLowerCase().includes(s)
    //         );
    //     });
    // },


};
</script>
<style>
a.quelle:link,a.quelle:visited,button.lolink
{
    color:#034798 !important;
    text-shadow:none !important;
    text-decoration:none  !important;
    font-weight:bold  !important;
}
a.quelle:link:hover,a.quelle:visited:hover,button.lolink:hover,
{
    color:#005bc7  !important;
    text-shadow:none !important;
    text-decoration:underline !important;
}
</style>
