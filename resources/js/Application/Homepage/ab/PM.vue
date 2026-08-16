<template>
  <Layout>
    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs" :current="'Private_Nachrichten'"></breadcrumb>
    </template>
    <MetaHeader title="Private Nachrichten" />

    <div class="max-w-none bg-layout-sun-100 dark:bg-layout-night-100 p-7 rounded-2xl shadow">

      <!-- Tabs -->
            <div class="flex border-b border-gray-300 dark:border-gray-700 mb-6 overflow-visible">
                <button
                v-for="tabItem in tabs"
                :key="tabItem.id"
                @click="changeTab(tabItem.id)"
                :class="[
                    'flex-1 text-center p-3 border-b-2 font-medium transition',
                    tab === tabItem.id
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent hover:text-gray-600 dark:hover:text-gray-300'
                ]"
                >
                {{ tabItem.icon }} {{ tabItem.label }}
                </button>
            </div>

      <!-- Inhalte -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow px-4 pt-1">

        <!-- Inbox -->
        <div v-if="tab === 'inbox'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">📥 Posteingang</h2>

                        <div class="my-6 flex justify-between items-center">
                            <search-filter
                                v-model="searchInbox"
                                class="w-full"
                                searchText=""
                                @reset="resetIn"
                            />
                        </div>


          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl overflow-visible w-full">
            <table class="min-w-full text-left border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="border border-gray-300 dark:border-gray-700 text-center pl-3" width="56">
                    <InputCheckbox
                      v-model="selectAllInbox"
                      @update:modelValue="toggleSelectAll('inbox', $event)"
                      name="select_all_inbox"
                    />
                  </th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="56">Gelesen</th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center min-w-[120px]" width="15%">Von</th>
                  <th class="border border-gray-300 dark:border-gray-700">Betreff</th>
                  <th class="border border-gray-300 dark:border-gray-700"></th>
                </tr>
              </thead>
              <tbody>
                    <tr v-for="msg in localInbox" :key="msg.id + '-' + msg.checked" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="border border-gray-300 dark:border-gray-700 text-center pl-3">
                <InputCheckbox
                :model-value="!!selectedInbox[msg.id]"
                @update:modelValue="val => setSelected('inbox', msg.id, val)"
                :name="'inbox_' + msg.id"
                :id="'inbox_' + msg.id"
                />
                </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center">
                    <PublishButton
                    :key="'pub-' + msg.id + '-' + msg.checked"
                    table="private_messages"
                    :id="msg.id"

                    :modelValue="msg.checked"
                    @update:modelValue="val => msg.checked = val"
                    :public="1"
                />
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 flex items-center">
                    <img :src="GetProfileImagePath(msg.avatar)" :alt="msg.user" :title="msg.user" class="w-8 h-8 rounded-full object-cover"/>
                    <span class="ml-2">{{ msg.user }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700">
                    <span class="font-bold cursor-pointer" @click="ShowMessage(msg)">{{ rumLaut(msg.subject) }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center pr-2">
                    <editbtns table="private_messages" :pm="true" :id="msg.id" :noedit="true"></editbtns>
                  </td>
                </tr>

                <tr v-if="localInbox.length === 0">
                  <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-300">Keine Nachrichten gefunden</td>
                </tr>
              </tbody>
            </table>

        </div>

          <!-- Buttons Inbox -->
          <div class="mt-4 flex gap-2">
            <button
              type="button"
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
              @click.stop.prevent="markAsRead"

            >
              Als gelesen markieren
            </button>
            <button
              class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              @click="deleteMessages('inbox')"

            >
              Löschen
            </button>
            <pagination basePath="pm/index/inbox" :links="inboxArr.links"></pagination>
          </div>


        </div>

        <!-- Outbox -->
        <div v-else-if="tab === 'outbox'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">📤 Gesendete Nachrichten</h2>

          <div class="my-6 flex justify-between items-center">
                            <search-filter
                                v-model="searchOutbox"
                                class="w-full"
                                searchText=""
                                @reset="resetOut"
                                />
                        </div>

          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl overflow-visible w-full">
            <table class="min-w-full text-left border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="border border-gray-300 dark:border-gray-700 text-center pl-3" width="56">
                    <InputCheckbox
                      v-model="selectAllOutbox"
                      @update:modelValue="toggleSelectAll('outbox', $event)"
                      name="select_all_outbox"
                    />
                  </th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="15%">An</th>
                  <th class="border border-gray-300 dark:border-gray-700">Betreff</th>
                  <th class="border border-gray-300 dark:border-gray-700"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(msg) in paginatedOutbox" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="border border-gray-300 dark:border-gray-700 text-center pl-3">
                    <InputCheckbox
                :model-value="!!selectedOutbox[msg.id]"
                @update:modelValue="val => setSelected('outbox', msg.id, val)"
                :name="'outbox_' + msg.id"
                :id="'outbox_' + msg.id"
                />

                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 flex items-center">
                    <img :src="GetProfileImagePath(msg.avatar)" :alt="msg.user" :title="msg.user" class="w-8 h-8 rounded-full object-cover"/>
                    <span class="ml-2">{{ msg.user }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700">
                    <span class="font-bold cursor-pointer" @click="ShowMessage(msg)">{{ rumLaut(msg.subject) }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center pr-2">
                    <editbtns table="private_messages" :pm="true" :id="msg.id" :noedit="true"></editbtns>
                  </td>
                </tr>

                <tr v-if="paginatedOutbox?.length === 0">
                  <td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-300">Keine Nachrichten gefunden</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Buttons Outbox -->
          <div class="mt-4 flex gap-2">
            <button
              class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              @click="deleteMessages('outbox')"

            >
              Löschen
            </button>
          </div>

          <!-- Pagination Outbox -->
          <!-- <div class="mt-4 flex justify-center space-x-1">
            <button
              v-for="page in outboxTotalPages"
              :key="page"
              @click="currentPageOutbox = page"
              :class="['px-3 py-1 rounded', currentPageOutbox === page ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300']"
            >
              {{ page }}
            </button>
          </div> -->

          <pagination basePath="pm/index/outbox" :links="outboxArr.links"></pagination>
        </div>

        <!-- Nachricht lesen -->
        <div v-else-if="tab === 'read' && selectedMessage" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">📖 Nachricht lesen</h2>

          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl flex flex-col md:flex-row gap-6">
            <!-- Linker Bereich: Nachricht -->
            <div class="flex-1 max-w-[70%]">
              <!-- KORRIGIERT: Vereinfachte Bedingungen für Antworten -->
              <p v-if="selectedMessage.public=='1' && selectedMessage.users_id != '4'"
                @click="answer(selectedMessage)"
                class="cursor-pointer font-bold text-[#58aaf8] hover:text-[#7cc0ff] transition mb-2"
              >
                ➡️ Antworten
              </p>
              <p v-if="selectedMessage.public == '2' && selectedMessage.users_id != '4'"
                @click="rewrite(selectedMessage)"
                class="cursor-pointer font-bold text-[#58aaf8] hover:text-[#7cc0ff] transition mb-2"
              >
                ➡️ Neue Nachricht an {{ selectedMessage.user }}
              </p>

              <p><strong>Von:</strong> {{ selectedMessage.user }}</p>
              <p><strong>Betreff:</strong> {{ selectedMessage.subject }}</p>
              <p><strong>Datum:</strong> {{ formatDate(selectedMessage.created_at) }}</p>
              <hr class="my-3 border-gray-300 dark:border-gray-600"/>
              <span v-html="(rumLaut(selectedMessage.message))"></span>
            </div>

            <!-- Rechter Bereich: Benutzerinfos -->
            <div class="w-full md:w-64 flex-shrink-0 bg-gray-100 dark:bg-gray-800 p-4 rounded-xl text-center self-start">

              <a :href="'/home/users/show/' + selectedMessage.user + '/' + selectedMessage.users_id">
                <img :src="GetProfileImagePath(selectedMessage.avatar || '008.jpg')" class="mx-auto w-24 h-24 rounded-full object-cover mb-2" />
              </a>
              <h4 class="font-semibold mb-1">Über {{ selectedMessage.user }}</h4>
              <p>Vorname: {{ selectedMessage.first_name }}</p>
              <p>Letzter Login:<br /> {{ formatDate(selectedMessage.lastlogin) }}</p>
              <p>Alter: {{ selectedMessage.age || '-' }}</p>
              <p>Website: <span v-if="selectedMessage.website"><a :href="selectedMessage.website">{{selectedMessage.website}}</a></span><span v-else>Keine</span></p>
            </div>
          </div>
        </div>

        <!-- Neue Nachricht -->
        <div v-else-if="tab === 'new'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">✉️ Neue Nachricht</h2>
          <InputSelectU
            width="100%"
            @input-change="updateFormData"
            id="to_id"
            v-model="to_id"
            :owner="UID"
            name="users_id"
            xname="to_id"
            :required="true"
          />
          <InputFormText
            v-model="subject"
            label="Betreff"
            id="subject"
            name="subject"
            :required="true"
          >
            <template #label>Betreff</template>
          </InputFormText>

          <InputHtml
            :modelValue="message"
            @update:modelValue="message = $event"
            class="w-full border dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 dark:text-gray-200"
            placeholder="Schreibe eine Nachricht..."
            rows="4"
            name="message"
          ></InputHtml>
          <button
            class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
            @click="sendMessage"
          >
            Senden
          </button>
        </div>

        <!-- Einstellungen -->
        <div v-else-if="tab === 'settings'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">⚙️ Einstellungen</h2>
          <div class="py-3 border-b border-gray-200 dark:border-gray-700">
            <MessageSettings :nohead="true">, siehe auch <a href='/admin/profile'>Profil</a></MessageSettings>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import editbtns from '@/Application/Components/Form/editbtns.vue';
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import MessageSettings from "@/Application/Shared/MessageSettings.vue";
import PublishButton from "@/Application/Components/Form/PublishButton.vue";
import { SD, GetProfileImagePath,rumLaut,nl2br, CleanTab, GetSettings } from "@/helpers";
import InputSelectU from "@/Application/Components/Form/InputSelectU.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import axios from "axios";
import { router } from '@inertiajs/vue3'
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import Pagination from "@/Application/Components/Pagination.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";

export default {
  components: {
    Layout,
    InputHtml,
    MetaHeader,
    editbtns,
    Breadcrumb,
    InputSelectU,
    InputFormText,
    PublishButton,
    InputCheckbox,
    MessageSettings,
    Pagination,
    SearchFilter,
  },

  props: {
    inboxArr: {
      type: Object,
      default: () => ({ data: [], links: [] })
    },
    outboxArr: {
      type: Object,
      default: () => ({ data: [], links: [] })
    },
    // form: { type: Array, default: () => [] },
    settings: { type: [Array, Object], default: () => [] },
  },

  data() {
    return {

      // 🔥 SAFE INIT (kein direct URL parsing im data!)
      tab: CleanTab("pm/index/") ?? 'inbox',
      localInbox: [],
//    form: {
//         search: this.filters?.search ?? "",
//       },
      message: "",
      subject: "",
      to_id: 0,

      tabs: [
        { id: "inbox", label: "Inbox", icon: "📥" },
        { id: "outbox", label: "Outbox", icon: "📤" },
        { id: "new", label: "Neue Nachricht", icon: "✉️" },
        { id: "settings", label: "Einstellungen", icon: "⚙️" },
      ],

      searchInbox: this.inboxArr?.filters?.search || '',
      searchOutbox: this.outboxArr?.filters?.search || '',

      selectedMessage: null,

      perPage: this.settings?.cnt_numrows || 10,

      UID:  null,

      selectAllInbox: 0,
      selectAllOutbox: 0,

      selectedInbox: {},
      selectedOutbox: {},

      // 🔥 IMPORTANT: prevents race condition crashes

    };
  },

  mounted() {
    console.log("TAB:", this.tab);
        this.localInbox = [...this.inboxArr.data];

    this.UID = window?.Laravel?.userId;
    // 🔥 URL sync SAFE (after mount only)
    const urlTab = new URLSearchParams(window.location.search).get('tab');
    if (urlTab) this.tab = urlTab;
  },

  computed: {

    // paginatedInbox() {
    //   return this.inboxArr.data;
    // },

    paginatedOutbox() {
      return this.outboxArr.data;
    },

    selectedInboxIds() {
      return Object.keys(this.selectedInbox)
        .filter(id => this.selectedInbox[id])
        .map(Number);
    },

    selectedOutboxIds() {
      return Object.keys(this.selectedOutbox)
        .filter(id => this.selectedOutbox[id])
        .map(Number);
    },
  },

  methods: {
    testClick() {

        alert("CLICK");
    },

    SD, GetProfileImagePath, rumLaut, nl2br, GetSettings, CleanTab,
 resetIn() {
      this.searchInbox =''
    },
    resetOut() {
      this.searchOutbox =''
    },
    // 🔥 FIXED TAB SWITCH (NO RACE CONDITION)
    changeTab(newTab) {
      if (this._navigating) return;

      this._navigating = true;
      this.tab = newTab;

      router.get('/pm/index/' + newTab, {
      }, {
        replace: true,
        preserveState: false,
        preserveScroll: true,
        only: ['inboxArr', 'outboxArr'],
        onFinish: () => {
          this._navigating = false;
        }
      });
    },

    setSelected(box, id, val) {
      const target = box === 'inbox'
        ? this.selectedInbox
        : this.selectedOutbox;

      if (val) target[id] = 1;
      else delete target[id];
    },

    toggleSelectAll(tab, value) {
        const list = tab === 'inbox'
        ? this.localInbox
        : this.paginatedOutbox;

      const target = tab === 'inbox'
        ? this.selectedInbox
        : this.selectedOutbox;

      list.forEach(msg => {
        if (value) target[msg.id] = 1;
        else delete target[msg.id];
      });

      if (tab === 'inbox') this.selectAllInbox = value;
      else this.selectAllOutbox = value;
    },

    async markAsRead() {

        if (!this.selectedInboxIds.length) return;

        this.localInbox.forEach(msg => {

            if (this.selectedInbox[msg.id]) {

                msg.checked = '1';
            }
        });

        // FORCE REACTIVE REFRESH
        this.localInbox = [...this.localInbox];

        await axios.post('/admin/pm/mark', {
            ids: this.selectedInboxIds.join(',')
        });

        this.selectedInbox = {};
        this.selectAllInbox = 0;

    },

    async deleteMessages(tab) {

    const ids = tab === 'inbox'
        ? this.selectedInboxIds
        : this.selectedOutboxIds;

    if (!ids.length) {
        alert("Keine Nachrichten ausgewählt");
        return;
    }

    if (!confirm(
        "Sind Sie sicher, dass Sie diese "
        + ids.length +
        " Einträge löschen möchten?"
    )) {
        return;
    }

    console.log("DELETE IDS:", ids);

    try {

        axios.delete('/admin/pm/delmore', {
            data: {
                ids: ids
            }
        });

        if (tab === 'inbox') {

            this.localInbox =
                this.localInbox.filter(
                    msg => !ids.includes(msg.id)
                );

        } else {

            this.localOutbox.data =
                this.outboxArr.data.filter(
                    msg => !ids.includes(msg.id)
                );
        }

        this.selectedInbox = {};
        this.selectedOutbox = {};

        this.selectAllInbox = false;
        this.selectAllOutbox = false;

    } catch (e) {

        console.error(e);
        alert("Fehler beim Löschen");
    }
},
async ShowMessage(msg) {

    this.localInbox = this.localInbox.map(m => {

        if (m.id === msg.id) {

            return {
                ...m,
                checked: '1',
            };
        }

        return m;
    });

    this.selectedMessage = {
        ...msg,
        checked: '1',
    };

    await axios.post('/admin/pm/mark', {
        ids: msg.id
    });

    if (!this.tabs.find(t => t.id === "read")) {

        this.tabs.unshift({
            id: "read",
            label: "Nachricht",
            icon: "📖"
        });
    }

    this.tab = "read";
},

    sendMessage() {
      const el = document.getElementById("editor_message");
      if (!el) return;

      const msg = el.innerHTML;

      if (!msg || !this.to_id || !this.subject) return;

      axios.post('/pm/save', {
        message: msg,
        to_id: this.to_id,
        subject: this.subject,
      }).then(() => {
        this.message = "";
        this.subject = "";
        this.to_id = null;

        this.changeTab("outbox");
      });
    },

    answer(msg) {
      this.tab = "new";
      this.to_id = msg.users_id;
      this.subject = msg.subject?.startsWith("Re:")
        ? msg.subject
        : "Re: " + msg.subject;
    },

    rewrite(msg) {
      this.tab = "new";
      this.to_id = msg.users_id;
      this.subject = "";
      this.message = "";
    },

    formatDate(date) {
      return new Date(date).toLocaleString();
    },
  },
  watch: {
        inboxArr: {
        handler(newVal) {
            this.localInbox = [...newVal.data];
        },
        deep: true,
        immediate: true
    },

  searchInbox(value) {
    router.get('/pm/index/inbox', {
      search: value
    }, {
      preserveState: true,
      replace: true,
      preserveScroll: true,
      only: ['inboxArr']
    });
  },

  searchOutbox(value) {
    router.get('/pm/index/outbox', {
      search: value
    }, {
      preserveState: true,
      replace: true,
      preserveScroll: true,
      only: ['outboxArr']
    });
  }
}
};
</script>
<style>
.w-fully{
    min-width:100% !important;
    max-width:100% !important;
}
.tw-row {
    @apply grid grid-cols-4 gap-4 place-items-center;
}

.tw-col {
    @apply flex items-center justify-center gap-2;
}
</style>
