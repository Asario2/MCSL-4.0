<template>
  <Layout>
    <MetaHeader :title="'Activity Log'" />
    <template #header>
            <breadcrumb :breadcrumbs="breadcrumbs" />
        </template>
                        <div class="flex justify-between items-center">
                    <search-filter
                        v-model="form.search"
                        class="w-full"

                        @reset="reset"
                    />
                </div>
    <div class="bg-layout-1 text-layout-1 p-6 rounded-lg shadow">

      <h2 class="text-lg font-semibold mb-4">Activity Log</h2>
      <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-layout-2 text-layout-1 text-xs uppercase">
            <tr>
              <th class="px-4 py-3">ID</th>
              <th class="px-4 py-3">Pub</th>
              <th class="px-4 py-3">Domain</th>
              <th class="px-4 py-3">Datum</th>
              <th class="px-4 py-3">Action</th>
              <th class="px-4 py-3">Tabelle</th>
              <th class="px-0 py-0" width="10">ID</th>
              <th class="px-4 py-3">URL</th>
              <th class="px-4 py-3">User</th>
              <th class="px-4 py-3">Info</th>
              <th class="px-4 py-3">IP</th>
              <th class="px-4 py-3">Session</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="row in paginatedLogs"
              :key="row.id"
              class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-800/60"
            >
              <td class="px-4 py-3 font-bold" :style="`background-color: ${bgcol(row.session_id)}; color: #000`">
                {{ row.id }}
              </td>

              <td class="p-0 pl-3">
                <span v-if="Number(checkedStatus?.[row.id]) === 1" style="font-size:24px;">✅</span>
                <span v-else>[]</span>
              </td>
              <td class="pl-3"><img :src="'/images/_' + row.dom + '/web/alogo.png'" class='w-5 h-5'/></td>
              <td class="p-0 pr-3">
                {{ getDate(row.created_at) }}
              </td>
              <td class="px-4 py-3">{{ row.action }}</td>
              <td class="px-4 py-3">{{ row.tablename }}</td>
              <td class="px-0 py-0">{{ row.excl_id }}</td>

              <td class="px-4 py-3">
                <button
                  v-if="row.URL"
                  @click="openModal('URL', row.URL)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                <span v-if="CheckOL()">
                  {{ row.URL.substr(22,35).replace("/admin/tables",'') }}
                </span>
                <span v-else>
                    {{ row.URL.substr(18,34).replace("/admin/tables",'') }}
                </span>
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>


              <td class="px-4 py-3">
                <div v-if="users?.[row?.users_id]">
                    <img
                    :src="GetProfileImagePath(users[row.users_id].img || '008.jpg')"
                    class="h-8 w-8 rounded-full object-cover"
                    :title="users[row.users_id].name"
                    :alt="users[row.users_id].name"
                    />
                </div>

                <div v-else>
                    <img
                    :src="'/images/users/profile_photo_path/008.jpg'"
                    class="h-8 w-8 rounded-full object-cover"
                    alt="Unbekannter Benutzer"
                    title="Unbekannter Benutzer"
                    />
                </div>
                </td>
                <!-- <img :src="GetProfileImagePath(users[row?.users_id]?.img)" class="h-8 w-8 rounded-full object-cover" :title="users[row?.users_id].name" :alt="users[row?.users_id].name"/></td> -->

              <td class="px-4 py-3">
                <button
                  v-if="row.info"
                  @click="openModal('Info', row.info)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  anzeigen
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>

              <td class="px-4 py-3">{{ row.IP }}</td>
              <td class="px-4 py-3 max-w-[200px] truncate"><img :src="'/images/icons/session.png'" class='w-8 h-8' :alt="'SessionID: ' + row.session_id" :title="'SessionID: ' + row.session_id"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
      >
        <div class="bg-white dark:bg-gray-900 w-full max-w-3xl rounded-xl shadow-xl">
          <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold">{{ modalTitle }}</h3>
            <button
              @click="showModal = false"
              class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-200"
            >
              ✕
            </button>
          </div>

          <pre class="p-4 text-xs overflow-auto max-h-[70vh] bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100"  v-html="modalContent"></pre>
        </div>
      </div>
        <!-- <pagination class="dark:bg-black"
            :links="pagination.links"
            basePath="admin/ActLog"
            @navigate="loadLogs" /> -->

<div class="flex flex-wrap gap-2 mt-4">

    <button
        class="px-3 py-1 border rounded"
        :disabled="currentPage === 1"
        @click="currentPage--"
    >
        ← Zurück
    </button>

    <button
        v-for="page in pageNumbers"
        :key="page"

        :disabled="page === '...'"
        @click="page !== '...' && (currentPage = page)"

        class="px-3 py-1 border rounded"

        :class="{
            'bg-blue-500 text-white': currentPage === page,
            'opacity-50 cursor-default': page === '...'
        }"
    >
        {{ page }}
    </button>

    <button
        class="px-3 py-1 border rounded"
        :disabled="currentPage >= totalPages"
        @click="currentPage++"
    >
       Weiter →
    </button>

</div>
    </div>
  </Layout>
</template>

<script>
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import axios from 'axios';
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import { CleanTable, ucf, SD, rumLaut, GetProfileImagePath,CheckOL } from "@/helpers";
import MetaHeader from '@/Application/Homepage/Shared/MetaHeader.vue';
import Pagination from "@/Application/Components/Pagination.vue";
import { nextTick } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  name: "ActivityLogTable",
  components: { Layout, Breadcrumb,MetaHeader,Pagination,SearchFilter },

  props: {
    users: [Object,Array],
    pag: [Object, Array],
    table_alt: String,
    table_q: String,
    table: { type: String, required: false },
    startPage: { type: Boolean, default: true },
    breadcrumbs: {
    type: [String, Object, Array],
    required:false,
    default: () => ({}),

    },
    // tables:[String,Object,Array],
  },

  data() {
    return {
      logs: [],
      pagination: [],
      currentPage: 1,
      perPage: 20,
      showModal: false,
      modalTitle: '',
      modalContent: '',
      checkedStatus: {},
      sortable: null,
      settings: {},
         form: {
            search: ''
        },

    };
  },

  computed: {

    pageNumbers() {

        const total = this.totalPages;
        const current = this.currentPage;

        let pages = [];

        // Alles anzeigen
        if (total <= 12) {

            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }

            return pages;
        }

        // Anfang
        if (current <= 9) {

            for (let i = 1; i <= 10; i++) {
                pages.push(i);
            }

            pages.push('...');
            pages.push(total);

            return pages;
        }

        // Ende
        if (current >= total - 8) {

            pages.push(1);
            pages.push('...');

            for (let i = total - 9; i <= total; i++) {
                pages.push(i);
            }

            return pages;
        }

        // Mitte

        pages.push(1);
        pages.push('...');

        let start = current - 3;
        let end = current + 3;

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        // IMMER zweites ...
        pages.push('...');

        pages.push(total);

        return pages;
    },
    tableHead() {
      return (this.logs[0]?.admin_table_id) ? "Tabelle" : "";
    },
    filteredLogs() {

        if (!this.form.search) {
            return this.logs;
        }

        const s = this.form.search.toLowerCase();
        return this.logs.filter(row => {

            const id        = String(row.id || '').toLowerCase();
            const action    = String(row.action || '').toLowerCase();
            const tablename = String(row.tablename || '').toLowerCase();
            const url       = String(row.URL || '').toLowerCase();
            const info      = String(row.info || '').toLowerCase();
            const ip        = String(row.IP || '').toLowerCase();
            const session   = String(row.session_id || '').toLowerCase();
            const dom       = String(row.dom || '').toLowerCase();
            const username  = String(row.user_name || '').toLowerCase();

            console.log('SEARCH:', s);
            console.log('USERNAME:', row.user_name, '→', username);
            console.log('MATCH USERNAME:', username.includes(s));

            return (
                id.includes(s) ||
                action.includes(s) ||
                tablename.includes(s) ||
                url.includes(s) ||
                info.includes(s) ||
                ip.includes(s) ||
                session.includes(s) ||
                dom.includes(s) ||
                username.includes(s)
            );
        });

    },

    paginatedLogs() {

        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;

        return this.filteredLogs.slice(start, end);

    },

    totalPages() {
        return Math.ceil(this.filteredLogs.length / this.perPage);
    }
},

async mounted() {
    if (typeof window !== "undefined") {
        window.addEventListener("beforeunload", this.markChecked);
    }

    await this.loadLogs();
    await this.fetchStatus();

    await nextTick();
    await this.markChecked();
},
  beforeUnmount() {
    if (typeof window !== "undefined") {
      window.removeEventListener("beforeunload", this.markChecked);
    }
  },
    watch: {
        // 'form.search': {
        //     handler(value) {

        //         this.loadLogs(
        //             '/api/activity-log?search=' + encodeURIComponent(value)
        //         );

        //     },
        //     deep: true
        // }

           'form.search'() {
        this.currentPage = 1;
    }
    },
  methods: {

    CleanTable, ucf, SD, rumLaut, GetProfileImagePath,CheckOL,
    bgcol(SID) {
        // einfache Hash-Funktion: wandelt String in Zahl, dann in hex
        let hash = 0;
        for (let i = 0; i < SID.length; i++) {
            hash = SID.charCodeAt(i) + ((hash << 5) - hash);
        }
        // letze 6 Stellen für Hex-Farbe
        let color = '#' + ((hash & 0x00FFFFFF).toString(16).padStart(6, '0'));
        return color;
    },
    getDate(ts) {
    if (!ts) return '';

    const [date, time] = ts.split(' ');
    const [year, month, day] = date.split('-');

    return `${day}.${month}.${year} ${time}`;
},
    async loadLogs(url = "/api/activity-log") {
        try {
            const res = await axios.get(url);

            this.logs = res.data.tables || [];
            this.pagination = res.data.pagination || [];
        } catch (error) {
            console.error("Fehler beim Activity Log laden:", error);
        }
    },


    reset() {
      this.form.search = "";
    },
markChecked() {

    const unchecked = this.logs
        .filter(row => Number(row.xkis_checked) === 0)
        .map(row => row.id);

    if (!unchecked.length) {
        return;
    }

    const blob = new Blob(
        [JSON.stringify({ ids: unchecked })],
        { type: "application/json" }
    );

    const ok = navigator.sendBeacon(
        "/activity-log/mark_all",
        blob
    );

    console.log("Beacon send:", ok, unchecked);
},

    async fetchStatus() {
      if (!this.logs || this.logs.length === 0) {
        return;
      }

      try {
        const response = await axios.get("/api/chkcom_log/");
        this.checkedStatus = response.data.success || {};
      } catch (error) {
        console.error("Fehler beim Batch-Status laden:", error);
      }
    },

    openModal(title, content) {
      this.modalTitle = title;
      if (title === "Matches") {
        try { content = typeof content === "string" ? JSON.parse(content) : content } catch(e){}
        this.modalContent = JSON.stringify(content,null,2);
      } else {
        this.modalContent = content;
      }
      this.showModal = true;
    },
  }
}
</script>

<style>
td { white-space: normal; word-wrap: break-word; overflow-wrap: break-word; }
.wwr { word-wrap: break-word; overflow-wrap: break-word; white-space: normal; }
.oton { background-color: rgb(50, 174, 179); border-radius: 50%; width: 24px !important; height: 24px; padding: 0px 3px; color: #fff; }
</style>
