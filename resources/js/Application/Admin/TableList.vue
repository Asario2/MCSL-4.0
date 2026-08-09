<template>
  <layout>
    <meta-header :title="'Übersicht der Tabelle Admin Table'" />
    <template #header>
<breadcrumb
    v-if="CleanTable() !== 'contacts'"
    :application-name="$page.props.applications.app_admin_name"
    :breadcrumbs="{
        'Liste der Tabellen': route('admin.tables.index'),
    }"
    current="Übersicht"
/>
    </template>

    <section class="mt-8">
      <list-container
        title="Liste der Tabellen"
        :datarows="rows"
        route-index="admin.tables.index"
        :filters="filters"
        :search-filter="true"
        :edit-on="false"
        route-edit="admin.tables.edit"
        :create-on="false"
        :route-create="routeCreate"
        :delete-on="false"
        route-delete="admin.tables.destroy"
        :view="rights.view[rows?.full_name]"
      >

        <template #header>
          <tr>
            <th class="np-dl-th-normal">Tabellenname</th>
            <th class="np-dl-th-normal">Beschreibung</th>
          </tr>
        </template>

        <!-- Tabellen Zeilen -->
<template #datarow="data">

    <template v-if="rights.view[data.datarow.full_name] == '1'">

        <td class="np-dl-td-normal">
            <a
                :href="'/admin/tables/' + lowercase(data.datarow.name) + '/show'"
                class="!text-blue-600 !dark:text-blue-600 hover:underline as"
            >
                {{ data.datarow.name }}
            </a>
        </td>

        <td class="np-dl-td-normal">
            {{ data.datarow.description }}
        </td>

    </template>

</template>

      </list-container>

    </section>
    <pagination class="dark:bg-black" :links="pag?.links" basePath="admin/tables" />
  </layout>
</template>

<script>
import { defineComponent } from "vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import ListContainer from "@/Application/Components/Lists/ListContainer.vue";
import { CleanTable,CheckTRights } from '@/helpers'; // NEU: Import der Batch-Funktionen
import { hasRightSync } from '@/utils/rights';
import { route } from 'ziggy-js';
import axios from "axios";
import Pagination from "@/Application/Components/Pagination.vue";
export default defineComponent({
  name: "Admin_TableList",

  components: {
    Layout,
    Breadcrumb,
    MetaHeader,
    ListContainer,
    Pagination,
  },

  props: {
    rows: {
      type: Object,
      required: true,
      default: () => ({ data: [] }),
    },
    pag:Object,
  },

  data() {
    return {
      filteredRows: [],
      filters: {},
      isLoading: true,
        rights: {
            view: {},

        }
    };
  },

  computed: {
    routeCreate() {
      const table = CleanTable();
      return route('admin.tables.create', table);
    },
  },

  async mounted() {

     this.rights.view = await this.getAllRights("view_table");


  this.isLoading = false





  },

  methods: {
    CleanTable,
    route,
    CheckTRights,
    lowercase(str) {
        return str.trim().toLowerCase().replace(/\s+/g, '_');
    },
    hasRight(right, table) {
            return hasRightSync(right, table);
        },
    async getAllRights(right) {
        const response = await axios.get(`/api/user/rights/des-all/${right}`);
//         console.log("RD" + response.data);
        return response.data;
    }
  },
});
</script>
