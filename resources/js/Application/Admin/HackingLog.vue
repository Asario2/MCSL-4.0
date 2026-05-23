<template>
    <MetaHeader title="HackLog" />

    <Layout>
    <template #header>
      <Breadcrumb :breadcrumbs="breadcrumbs" />
    </template>
    <div class="p-6 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
        <div class="flex justify-between items-center mb-4">

    <search-filter
        v-model="form.search"
        class="w-full"
    />

</div>
      <h2 class="text-xl font-semibold mb-4">HackLog</h2>

      <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-3 py-2 text-left">Datum</th>
              <th class="px-3 py-2 text-left">DOM</th>
              <th class="px-3 py-2 text-left">IP</th>
              <th class="px-3 py-2 text-left">Number</th>
              <th class="px-3 py-2 text-left">URL</th>
              <th class="px-3 py-2 text-left">Gesperrt bis</th>
              <th class="px-3 py-2 text-left">Method</th>
              <th class="px-3 py-2 text-left">Score</th>
              <th class="px-3 py-2 text-left">Matches</th>
              <th class="px-3 py-2 text-left">Agent</th>
              <th class="px-3 py-2 text-left"></th>

            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, index) in paginatedLogs"
              :key="index"
              class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              <td class="px-3 py-2 whitespace-nowrap">{{ row.created_at }}</td>
              <td class="px-3 py-2 font-mono"><img :src="'/images/_' + row.dom + '/web/alogo.png'" class='w-4 h-4'/></td>
              <td class="px-3 py-2 font-mono">{{ row.ip }}</td>
              <td class="px-3 py-2 font-mono">{{ row.violations }}</td>
              <td class="px-3 py-2 truncate max-w-xs">
                <button
                    v-if="row.url"
                    @click="openModal('URL', row.url)"
                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-left"
                >
                    {{ row.url.substr(0,29) }}
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>

              <td class="px-3 py-2 whitespace-nowrap">{{ formatDateTime(row.banned_until) }}</td>
              <td class="px-3 py-2">{{ row.method }}</td>
              <td class="px-3 py-2 font-semibold" :class="row.score > 0 ? 'text-red-500' : ''">
                {{ row.score }}
              </td>
              <td class="px-3 py-2">
                <button
                  v-if="row.matches"
                  @click="openModal('Matches', row.matches)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  anzeigen
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>
              <td class="px-3 py-2 truncate max-w-xs">
                <button
                  v-if="row.agent"
                  @click="openModal('Agent', row.agent)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  anzeigen
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>
              <td>
                <delhackinglog :id="row.id"></delhackinglog>
              </td>
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

          <pre class="p-4 text-xs overflow-auto max-h-[70vh] bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
{{ modalContent }}
          </pre>
        </div>
      </div>
    </div>
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
  </Layout>
</template>

<script>
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import delhackinglog from "@/Application/Admin/Shared/delHackingLog.vue"
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
export default {
  name: 'RequestLogTable',
  components: {
    Layout,
    delhackinglog,
    MetaHeader,
    Breadcrumb,
    SearchFilter,

  },
  props: {
    tables: {
      type: Array,
      required: true,
    },
    breadcrumbs:{
        type: Object,
        required: false,
        default: () => [],
    },
  },
  data() {
    return {
      showModal: false,
      modalTitle: '',
      modalContent: '',
        currentPage: 1,
        perPage: 20,

        form: {
            search: ''
        }
    }
  },
    watch: {
        'form.search'() {
            this.currentPage = 1;
        }
    },
  computed: {

    filteredLogs() {

        if (!this.form.search) {
            return this.tables;
        }

        const s = this.form.search.toLowerCase();

        return this.tables.filter(row => {

            return (
                String(row.id || '').toLowerCase().includes(s) ||
                String(row.ip || '').toLowerCase().includes(s) ||
                String(row.url || '').toLowerCase().includes(s) ||
                String(row.dom || '').toLowerCase().includes(s) ||
                String(row.method || '').toLowerCase().includes(s) ||
                String(row.agent || '').toLowerCase().includes(s) ||
                String(row.score || '').toLowerCase().includes(s) ||
                String(row.violations || '').toLowerCase().includes(s) ||
                String(row.created_at || '').toLowerCase().includes(s)
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
    },

    pageNumbers() {

        const total = this.totalPages;
        const current = this.currentPage;

        let pages = [];

        if (total <= 12) {

            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }

            return pages;
        }

        if (current <= 9) {

            for (let i = 1; i <= 10; i++) {
                pages.push(i);
            }

            pages.push('...');
            pages.push(total);

            return pages;
        }

        if (current >= total - 8) {

            pages.push(1);
            pages.push('...');

            for (let i = total - 9; i <= total; i++) {
                pages.push(i);
            }

            return pages;
        }

        pages.push(1);
        pages.push('...');

        let start = current - 3;
        let end = current + 3;

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        pages.push('...');
        pages.push(total);

        return pages;
    }
},
  methods: {
  formatDateTime(datetime) {
    if (!datetime) return "";

    const [datePart, timePart] = datetime.split(" ");
    const [year, month, day] = datePart.split("-");

    return `${day}.${month}.${year} ${timePart}`;
},
    openModal(title, content) {
      this.modalTitle = title

      if (title === 'Matches') {
        try {
          content = typeof content === 'string'
            ? JSON.parse(content)
            : content
        } catch (e) {
          // fallback: leave as string
            this.asd = e;
        }
        this.modalContent = JSON.stringify(content, null, 2)
      } else {
        this.modalContent = content
      }

      this.showModal = true
    },
  },
}
</script>
