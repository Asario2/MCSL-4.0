<template>
    <Layout>
        <MetaHeader title="Kontakte"></MetaHeader>
      <div class="p-4 bg-gray-900 text-white min-h-screen">
        <FileUploadModal
                             v-model:isModalOpen="modals.fileup"
                        :column="'fileup'"

                        :is_imgdir = "false"
                        @close="modals['fileup'] = false"
                        @open-import-contacts="openImportModal"




                        @imageUploaded="handleImageUploaded('fileup', $event)"
                        v-model="fileval"
                        :namee="fileval"
                        :alt_path="'_' + SD() + '/' + CleanTable_alt() + '/' + 'fileup'"
                        :domain="subdomain"
                        :tablex="table_x"
                        :path="tablex"
                        :ref="'fileup'"
                        :value="imageId"
                        :image="fileval"
                        :namee2="'fileup'"
                        :Message="false"
                    />
        <div class="flex items-center justify-between w-full">

    <!-- 🔵 LINKS -->
    <div>
        <addbtn table="contacts" text="Neuer Kontakt" :safe="true" />
    </div>

    <!-- 🔵 RECHTS -->
    <div class="flex items-center space-x-3">

        <importBtn />

        <button type="button" @click="openinfo('ios')" class="flex items-center">
            <IconIOS class="w-6 h-6 mr-1" />
            <b>IOS</b>
        </button>

        <button type="button" @click="openinfo('android')" class="flex items-center">
            <IconAndroid class="w-6 h-6 mr-1" />
            <b>Android</b>
        </button>

        <button type="button" @click="openModal('fileup')" class="flex items-center">
           <IconUpload class="w-6 h-6 mr-2"/> <b>CSV Importieren</b>
        </button>

    </div>

    <input type="hidden" :id="'fileup'" :name="'fileup'" v-model="fileval" />
    <help
        v-if="showHelpModal"
        :info_type="info_type"
        @close="showHelpModal = false"
    />

    <ImportContactsModal
        v-if="showImportModal"
        :contacts="importContacts"
        @close="showImportModal = false"
        @saved="handleSaved"
    />
</div>

            <div class="flex justify-between items-center">
                <search-filter
                v-if="searchFilter"
                v-model="form.search"
                class="w-full"
                ref="searchField"
                @reset="reset"
                />

                </div>


        <template v-if="hasContacts">
          <template v-for="letter in sortedLetters" :key="letter">
            <div class="mb-6 rounded-lg overflow-hidden border border-black">

              <!-- Buchstaben-Trenner -->
              <div class="px-4 py-2 font-bold text-gray-300 text-lg bg-gray-800">
                {{ letter }}
              </div>

              <!-- Tabelle -->
              <table class="min-w-full table-auto border-collapse">
                <thead>
                  <tr class="bg-gray-700">
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="1%">Mehr</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="1%">Gruppe</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Name</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Vorname</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Nachname</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="1%">Email</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Telefon</th>
                    <th class="px-4 py-2 border-b border-black text-left" width="14%">Handy</th>
                    <th class="px-4 py-2 border-b border-black text-left" width="1%">Aktionen</th>
                  </tr>
                </thead>
                <tbody>

                  <!-- Kontakt-Zeilen mit aufklappbaren Details -->
                  <template v-for="(contact, index) in groupedContacts[letter]" :key="contact.id">
                    <!-- Normale Zeile -->
                    <tr
                      class="hover:bg-gray-800"
                      :class="{ 'border-b border-black': index !== groupedContacts[letter].length - 1 }"
                    >
                      <!-- Info-Button -->
                      <td class="px-2 py-1 border-r border-black text-center">
                        <button
                          v-if="contact.Kommentar || contact.Adresse || contact.Geburtsdatum"
                          @click="toggleDetails(contact.id)"
                          class="text-blue-400 hover:text-blue-200 font-bold text-3xl"
                        >
                          ⓘ
                        </button>
                      </td>

                      <!-- Gruppen-Icon -->
                    <td class="px-2 py-1 border-r border-black text-center relative">

                    <div class="relative inline-block">

                        <!-- Aktuelles Icon als Button im weißen Kreis -->
                        <div
                        class="w-[30px] h-[30px] bg-white rounded-full flex items-center justify-center cursor-pointer shadow hover:scale-110 transition mr-2"
                        @click.stop="toggleGroupPicker(contact.id)"
                        :title="contact.Gruppe"
                        >
                        <img
                            :src="`/images/icons/Con_Groups/${contact.Gruppe}.gif`"
                            class="w-5 h-5"
                        />
                        </div>

                        <!-- Auswahl bleibt offen -->
                        <div
                        v-if="activeGroupPicker === contact.id"
                        class="absolute left-10 top-1/2 -translate-y-1/2 flex space-x-1 p-1 rounded shadow-lg z-50"
                        >
                        <div
                            v-for="group in groups.filter(g => g !== contact.Gruppe)"
                            :key="group"
                            class="w-[30px] h-[30px] bg-white rounded-full flex items-center justify-center cursor-pointer shadow hover:scale-110 transition"
                            @click.stop="changeGroup(contact, group)"
                            :title="group"
                        >
                            <img
                            :src="`/images/icons/Con_Groups/${group}.gif`"
                            class="w-5 h-5"
                            />
                        </div>
                        </div>

                    </div>

                    </td>
                      <!-- Kontaktfelder -->
                      <td class="px-4 py-1 border-r border-black font-medium" v-html="rumLaut(contact.Name) || '-'"></td>
                      <td class="px-4 py-1 border-r border-black" v-html="rumLaut(contact.Vorname) || '-'"></td>
                      <td class="px-4 py-1 border-r border-black" v-html="rumLaut(contact.Nachname) || '-'"></td>
                      <td class="px-4 py-1 border-r border-black">
                        <span v-if="contact.Email">
                          <a :href="'mailto:' + contact.Email" :alt="contact.Email" :title="contact.Email ">
                            <img :src="'/images/icons/mail.png'">
                          </a>
                        </span>

                      </td>
                      <td class="px-4 py-1 border-r border-black">{{ contact.Telefon || '-' }}</td>
                      <td class="px-4 py-1 border-black">{{ contact.Handy || '-' }}</td>
                      <td class="px-4 py-1 border-black"><editbtns v-if="contact.us_poster == UID" table="contacts" :id="contact.id" :uid="contact.us_poster" :safe="false"></editbtns></td>
                    </tr>

                    <!-- Detail-Zeile -->
                    <tr v-if="isExpanded(contact.id)" :key="contact.id + '-details'">
                      <td colspan="9" class="px-4 py-2 bg-gray-800 text-gray-200">
                        <span v-html="`
                            ${contact.Kommentar ? contact.Kommentar + '<br />' : ''}
                            ${contact.Adresse ? contact.Adresse + '<br />' : ''}
                            ${contact.Geburtsdatum ? 'Geburtstag: ' + contact.Geburtsdatum + '<br />' : ''}
                            ${contact.ripdate ? 'Todestag: ' + contact.ripdate : ''}
                                    `
                        "></span>
                      </td>
                    </tr>
                  </template>

                </tbody>
              </table>
            </div>
          </template>
        </template>

        <!-- Keine Kontakte gefunden -->
        <div v-else class="text-center py-8 text-gray-400">
          Keine Kontakte gefunden
        </div>

      </div>
    </Layout>
  </template>
<script>
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Help from "@/Application/Components/HelpModal.vue";
import { rumLaut } from "@/helpers";
import axios from "axios";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Addbtn from "@/Application/Components/Form/addbtn.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import FileUploadModal from "@/Application/Components/FileUploadModal.vue";
import ImportContactsModal from "@/Application/Admin/ImportContactsModal.vue"
import { CleanTable_alt,CleanTable,SD } from "@/helpers";
import IconIOS from "@/Application/Components/Icons/IconIOS.vue";
import IconAndroid from "@/Application/Components/Icons/IconAndroid.vue";
import IconUpload from "@/Application/Components/Icons/IconUpload.vue";


export default {
  name: 'ContactTable',
  components: {
    Layout,
    editbtns,
    Addbtn,
    SearchFilter,
    MetaHeader,
    FileUploadModal,
    ImportContactsModal,
    IconIOS,
    IconAndroid,
    IconUpload,
    Help,
  },
  props: {
    contacts: {
      type: Array,
      required: true,
      default: () => []
    },
    searchFilter: {
      type: Boolean,
      default: true,
    },
    filters: {
      type: Object,
      default: () => ({}),
    },
    searchText: {
      type: String,
      default: "Hier kannst du den Suchbegriff eingeben",
    }
  },
  data() {
    return {
      form: {
        search: this.filters?.search ?? "",
      },
      groups: ['Kunde','Friends','Projects','Offiziell','Familie'],
      activeGroupPicker: null, // speichert contact.id
      expandedRows: [],
      searchTimeout: null,
      isLoading: false,
      loading: false,
      modals: {},
      fileval: null,
    showImportModal: false,
    importContacts: [],
    showHelpModal: false,
     info_type: null,

    //   kontakteGrouped: {},  // nicht null oder undefined

    }
  },
  watch: {
    // 'form.search': {
    //   handler: throttle(function (newSearch) {
    //     const query = pickBy({ search: newSearch });
    //     this.$inertia.get(
    //       this.route("admin.kontakte"),
    //       query,
    //       {
    //         preserveState: true,
    //         replace: true,
    //       }
    //     );
    //   }, 300),
    //   immediate: false,
    // },
  },
  computed: {
    hasContacts() {
      return Array.isArray(this.contacts) && this.contacts.length > 0;
    },
   filteredContacts() {
    if (!this.form.search) return this.contacts;

    const s = this.form.search.toLowerCase();

    return this.contacts.filter(c => {
      return (
        (c.Name && c.Name.toLowerCase().includes(s)) ||
        (c.Vorname && c.Vorname.toLowerCase().includes(s)) ||
        (c.Nachname && c.Nachname.toLowerCase().includes(s)) ||
        (c.Email && c.Email.toLowerCase().includes(s)) ||
        (c.Adresse && c.Adresse.toLowerCase().includes(s)) ||
        (c.Kommentar && c.Kommentar.toLowerCase().includes(s)) ||
        (c.Telefon && c.Telefon.toLowerCase().includes(s)) ||
        (c.Geburtsdatum && c.Geburtsdatum.toLowerCase().includes(s)) ||
        (c.ripdate && c.ripdate.toLowerCase().includes(s)) ||
        (c.Gruppe && c.Gruppe.toLowerCase().includes(s)) ||
        (c.Handy && c.Handy.toLowerCase().includes(s))
      );
    });
  },

 groupedContacts() {
  const grouped = {};

  this.filteredContacts.forEach(contact => {
    const firstLetter = contact.Name?.charAt(0).toUpperCase() ?? "#";

    if (!grouped[firstLetter]) grouped[firstLetter] = [];
    grouped[firstLetter].push(contact);
  });

  // Optional: Kontakte innerhalb eines Buchstaben segments alphabetisch sortieren
  Object.keys(grouped).forEach(letter => {
    grouped[letter].sort((a, b) => {
      return (a.Name ?? "").localeCompare(b.Name ?? "", 'de');
    });
  });

  return grouped;
}       ,
  UID(){
      return window.authid;
    },
sortedLetters() {
  if (!this.groupedContacts || typeof this.groupedContacts !== "object") {
    return [];
  }

  // Nur erste Buchstaben, keine Duplikate
  const letters = Object.keys(this.groupedContacts)
    .map(l => l.toUpperCase()) // alles groß
    .sort((a, b) => a.localeCompare(b, 'de')); // deutsche Sortierung optional

  return letters;
},
  },
  methods: {
toggleGroupPicker(id) {
  this.activeGroupPicker =
    this.activeGroupPicker === id ? null : id;
},
closeGroupPicker() {
  this.activeGroupPicker = null;
},
    async changeGroup(contactId, newGroup) {
        try {
//             console.log((contactId));
            await axios.put(`/admin/contacts/${contactId.id}/group`, {
                Gruppe: newGroup
            });
            // optional: UI aktualisieren, z.B. reload der Kontakte
            this.$inertia.reload({ only: ['contacts'] });
            window.toastBus.emit({ type: 'success', message: 'Gruppe erfolgreich geändert!' });
             this.activeGroupPicker = null;
        } catch (error) {
            console.error('Fehler beim Ändern der Gruppe:', error);
            window.toastBus.emit({ type: 'error', message: 'Gruppe konnte nicht geändert werden.' });
        }
    },

        CleanTable_alt,
        SD,
        CleanTable,

 handleSaved() {
            this.showImportModal = false;

            window.toastBus.emit({type:"success",message:"Kontakte erfolgreich gespeichert"});

            this.$inertia.reload({ only: ['contacts'] });
        },
        openinfo(type){
            this.info_type = type;

            this.showHelpModal=true;
        },
        openImportModal(contacts) {
            this.importContacts = contacts;
            this.showImportModal = true;
        },
        handleImageUploaded(fieldName) {
            // Aktualisieren Sie den Feldwert
            // this.localFfo.original[fieldName].value = fileName;

            // Vorschau aktualisieren (Vue 3 way - kein $set mehr nötig)
            // if(CleanTable() != "users")
            // {
            //     this.thumb = "thumbs/";

            // }
            // else{
            //     this.thumb ='';
            // }
            // this.previewImages[fieldName] = `/images/_${this.subdomain}/${this.CleanTable_alt()}/${fieldName}/${this.thumb}${fileName}`;

            // Schließen Sie das Modal
            this.modals[fieldName] = false;
        },
    reset() {
      this.form.search = "";
    },
    openModal(name) {
            this.modals[name] = true;

        },
         closeModal(name) {
            this.modals[name] = false;
        },
        handleModalClose(fieldName) {
//     console.log('Modal close requested, but checking if we should close...');

    // Nur schließen wenn wirklich gewünscht, nicht nach Upload
    // Sie können hier eine Bedingung hinzufügen
    this.modals[fieldName] = false;
  },
    rumLaut,
    handleImageError(event) {
      event.target.style.display = 'none';
    },
    toggleDetails(id) {
      if (this.expandedRows.includes(id)) {
        this.expandedRows = this.expandedRows.filter(r => r !== id);
      } else {
        this.expandedRows.push(id);
      }
    },
    isExpanded(id) {
      return this.expandedRows.includes(id);
    }
  },
  mounted() {
    const params = new URLSearchParams(window.location.search);
    const search = params.get("search");
    document.addEventListener('click', this.closeGroupPicker);
    if (search && search.trim() !== "") {
      this.isLoading = false;
      this.loading = false;
    } else {
      this.isLoading = false;
    }

},
beforeUnmount() {
  document.removeEventListener('click', this.closeGroupPicker);
},

};
</script>

  <style scoped>
  .circle {
    background-image:url("/images/icons/Con_Groups/circle.png");
    background-repeat:no-repeat;
    background-position:center center;
  }

  td:empty::before {

    color: #6b7280;
  }

  button {
    cursor: pointer;
    background: none;
    border: none;
  }
  </style>

