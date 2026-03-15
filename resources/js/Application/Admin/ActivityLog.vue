<template>
  <Layout>
    <MetaHeader :title="'Activity Log'" />
    <template #header>
            <breadcrumb :breadcrumbs="breadcrumbs" />
        </template>
    <div class="bg-layout-1 text-layout-1 p-6 rounded-lg shadow">

      <h2 class="text-lg font-semibold mb-4">Activity Log</h2>

      <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-layout-2 text-layout-1 text-xs uppercase">
            <tr>
              <th class="px-4 py-3">ID</th>
              <th class="px-4 py-3">Pub</th>
              <th class="px-4 py-3">Datum</th>
              <th class="px-4 py-3">Action</th>
              <th class="px-4 py-3">Tabelle</th>
              <th class="px-4 py-3">ID</th>
              <th class="px-4 py-3">URL</th>
              <th class="px-4 py-3">User</th>
              <th class="px-4 py-3">Info</th>
              <th class="px-4 py-3">IP</th>
              <th class="px-4 py-3">Session</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="row in logs"
              :key="row.id"
              class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-800/60"
            >
              <td class="px-4 py-3 font-medium">{{ row.id }}</td>

              <td class="px-4 py-3">
                <span v-if="Number(checkedStatus?.[row.id]) === 1" style="font-size:24px;">✅</span>
                <span v-else>[]</span>
              </td>
              <td class="px-4 py-3">
                {{ getDate(row.created_at) }}
              </td>
              <td class="px-4 py-3">{{ row.action }}</td>
              <td class="px-4 py-3">{{ row.tablename }}</td>
              <td class="px-4 py-3">{{ row.excl_id }}</td>

              <td class="px-4 py-3">
                <button
                  v-if="row.URL"
                  @click="openModal('URL', row.URL)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  {{ row.URL.substr(0,40) }}
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>


              <td class="px-4 py-3"><img :src="GetProfileImagePath(users[row?.users_id].img)" class="h-8 w-8 rounded-full object-cover"/></td>

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

    </div>
  </Layout>
</template>

<script>
import axios from 'axios';
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import { CleanTable, ucf, SD, rumLaut, GetProfileImagePath } from "@/helpers";
import MetaHeader from '@/Application/Homepage/Shared/MetaHeader.vue';

export default {
  name: "ActivityLogTable",
  components: { Layout, Breadcrumb,MetaHeader },

  props: {
    users: [Object,Array],
    pag: [Object, Array],
    table_alt: String,
    table_q: String,
    table: { type: String, required: false },
    startPage: { type: Boolean, default: true },
    breadcrumbs:String,
  },

  data() {
    return {
      logs: [],
      showModal: false,
      modalTitle: '',
      modalContent: '',
      checkedStatus: {},
      sortable: null,
      settings: {},
    };
  },

  computed: {
    tableHead() {
      return (this.logs[0]?.admin_table_id) ? "Tabelle" : "";
    }
  },

  async mounted() {
//     console.log("ActivityLogTable mounted");
    this.loadLogs();

    // Beim verlassen der Seite
    window.addEventListener("beforeunload", this.markChecked);
  },

  beforeUnmount() {
    window.removeEventListener("beforeunload", this.markChecked);
  },

  methods: {

    CleanTable, ucf, SD, rumLaut, GetProfileImagePath,
    getDate(ts) {
    if (!ts) return '';

    const [date, time] = ts.split(' ');
    const [year, month, day] = date.split('-');

    return `${day}.${month}.${year} ${time}`;
},
    loadLogs() {
//       console.log("Loading logs...");
      axios.get("/api/activity-log")
        .then(res => {
//           console.log("Logs geladen:", res.data);
          this.logs = res.data;
          this.fetchStatus();
        })
        .catch(err => console.error("Fehler beim Laden:", err));
    },

    markChecked() {
      const unchecked = this.logs
        .filter(row => row.pub === 0)
        .map(row => row.id);

      if (!unchecked.length) return;

//       console.log("Setze pub=1 für IDs:", unchecked);

      // Frontend sofort updaten
      this.logs.forEach(row => {
        if (unchecked.includes(row.id)) row.pub = 1;
      });

      // sendBeacon an Backend
      const blob = new Blob([JSON.stringify({ ids: unchecked })], { type: "application/json" });
      navigator.sendBeacon("/api/activity-log/check", blob);
    },

    async fetchStatus() {
      await this.$nextTick();
      if (!this.logs || this.logs.length === 0) return;
      try {
        const response = await axios.get("/api/chkcom_log/");
        this.checkedStatus = response.data.success;
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
