<template>
    <Layout>
        <MetaHeader title="Rechtsanwalt Christian Henning" />
        <div>

  <div class="flex justify-between items-center">
                <search-filter
                v-if="searchFilter"
                v-model="form.search"
                class="w-full"
                ref="searchField"
                @reset="reset"
                /></div>
<div>
<table class="w-full table-fixed">
  <tbody>
    <tr
      v-for="(db) in filteredContacts"
      :key="db.pid"
        class="odd:bg-gray-100/40 even:bg-transparent hover:bg-gray-300/60 transition-colors"
    >
      <td width="75%" class="pl-3">
        <h2 class="bl mt-0 mb-0" v-html="rumLaut(db.name)"></h2>
        <span class="text-sm">
          <a class="quelle mt-[-4px]" :href="db.quellurl">
            {{ rumLaut(db.qname) }}
          </a>
          - <b>Ausgabe:</b> {{ db.QuellenAusgabe || '-' }}
          <b>Seite:</b> {{ db.QuellenSeite || '-' }}
        </span>
      </td>

      <td width="15%">
        <a
          class="inline-flex items-center gap-2 whitespace-nowrap quelle"
          :href="'files/_chh/publikationen/file_pdf/' + db.file_pdf"
        >
          <img :src="'/images/icons/PDF.png'" class="h-8" />
          <span>Download ({{ db.filesize }})</span>
        </a>
      </td>

      <td v-if="hasr" class="text-right">
        <editbtns table="publikationen" :id="db.pid" />
      </td>
    </tr>
  </tbody>
</table>


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
export default {
    name: 'NewHome',
    components: { Layout, MetaHeader,editbtns, SearchFilter, Pagination},
    props:{
        data:[Object,Array],
        datb:[Object,Array],
        pag:[Object,Array],

    },
    data() {
        return {
            searchFilter: true,
             form: {
            search: ""
        }
        }
    },
    methods: {
        nl2br,
        rumLaut,
        GetRights,
    // andere Methoden hier...
  },
  computed:
  {
    hasr(){
    return GetRights("edit","publikationen");
    },
    filteredContacts() {
        if (!this.form.search) return this.data;

        const s = this.form.search.toLowerCase();

        return this.data.filter(c => {
            return [
                c.name,
                c.QuellenAusgabe,
                c.QuellenSeite,
                c.file_pdf,
                c.qname,
                c.QuellUrl
            ].some(val =>
                val && String(val).toLowerCase().includes(s)
            );
        });
    },

  }
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
