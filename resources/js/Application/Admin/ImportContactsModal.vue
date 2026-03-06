<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center"
    style="z-index:1000"
  >
    <div
      class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-[1400px] max-h-[80vh] overflow-auto pt-0"
    >

      <!-- HEADER -->
    <div
        class="sticky top-0 z-10
                flex items-center justify-between
                mb-4 p-0
                bg-white dark:bg-gray-800"
        >
        <h2 class="text-xl font-bold">Kontakte Importieren</h2>

        <button
            type="button"
            @click="$emit('close')"
            class="w-8 h-8 flex items-center justify-center rounded-full
                text-2xl font-bold text-gray-500
                hover:bg-gray-200 dark:hover:bg-gray-700
                hover:text-red-500 transition"
        >
            &times;
        </button>
    </div>

      <!-- TABLE -->
      <table class="w-full mb-4" cellspacing="1" cellpadding="4">
        <thead>
          <tr class="bg-gray-700">
            <th class="px-4 py-2 border-b border-r border-black text-left flex items-center space-x-2">
              <InputCheckbox
                name="check_all"
                id="check_all_id"
                :model-value="contacts.every(c => c.selected) ? 1 : 0"
                @click="check_all"
                class="cursor-pointer"
              />
              <span></span>
            </th>

            <th class="px-4 py-2 border-b border-r border-black text-left">Name</th>
            <th class="px-4 py-2 border-b border-r border-black text-left">Vorname</th>
            <th class="px-4 py-2 border-b border-r border-black text-left">Nachname</th>
            <th class="px-4 py-2 border-b border-r border-black text-left">Email</th>
            <th class="px-4 py-2 border-b border-r border-black text-left">Telefon</th>
            <th class="px-4 py-2 border-b border-r border-black text-left">Handy</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(c, index) in contacts"
            :key="index"
            class="hover:bg-gray-900 border-b border-black"
          >
            <td class="pl-4">
              <InputCheckbox
                :id="c.id"
                :name="'cb_' + c.id"
                v-model="c.selected"
                :checked="true"
                value="1"
              />
            </td>

            <td class="mr-2">{{ c.full_name }}</td>
            <td class="mr-2">{{ c.first_name }}</td>
            <td class="mr-2">{{ c.last_name }}</td>
            <td class="mr-2">{{ c.email }}</td>

            <td class="mr-2">
              <span v-for="t in c.phones" :key="t">
                {{ t }}<br />
              </span>
            </td>

            <td class="mr-2">
              <span v-for="m in c.mobiles" :key="m">
                {{ m }}<br />
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- SAVE BUTTON -->
      <div class="flex justify-end">
        <button
          @click="saveSelected"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          Speichern
        </button>
      </div>

    </div>
  </div>

</template>

<script>

import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import axios from "axios";
export default {
  props: {
    contacts: Array,
  },
  components:{
    InputCheckbox,

  },
  data() {
    return {};
  },
  methods: {
      check_all() {
    const allChecked = this.contacts.every(c => c.selected);
    this.contacts.forEach(c => c.selected = !allChecked);
  },
  saveSelected() {
    const selectedContacts = this.contacts.filter(c => c.selected);
       if (selectedContacts.length === 0) {
        alert("Bitte Kontakt auswählen");
        return;
    }
    axios.post('/api/contacts/import', { contacts: selectedContacts })
      .then(() => {
        this.$emit('saved');
        // alert('Kontakte erfolgreich gespeichert!');
        //  this.$emit('close');
      })
      .catch(err => {
        console.error(err);
        alert('Fehler beim Speichern!');
      });
  }
}
,
};
</script>
