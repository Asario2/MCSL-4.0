<template>
  <div class="p-4 space-y-4">

    <!-- TEXT INPUT -->
        <div class="fixed top-0 left-0 right-0 bg-white border-b p-4 flex items-center gap-2 z-50 shadow">
      <input
        v-model="textInput"
        type="text"
        placeholder="Text eingeben..."
        class="border p-2 rounded flex-1"
      />
      <button
        @click="applyFilter"
        class="bg-blue-500 text-white px-4 py-2 rounded"
      >
        Filter anwenden
      </button>
      <button
        @click="resetSelection"
        class="bg-gray-500 text-white px-4 py-2 rounded"
      >
        Auswahl zurücksetzen
      </button>
    </div>

    <!-- Hinweis -->
    <div v-if="!textInput" class="text-gray-500">
      Bitte Text eingeben, um Vorschau zu sehen
    </div>

    <!-- Fonts untereinander -->
    <div v-if="textInput && displayedFonts.length" class="space-y-4">
      <div
        v-for="font in displayedFonts"
        :key="font"
        class="flex items-center gap-4 border p-2 rounded"
      >
        <!-- Bild -->
        <img
          :src="getImage(font)"
          class="w-[40%] h-auto border"
        />

        <!-- Checkbox + Label -->
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="selected" :value="font" :id="font" />
          <label :for="font">{{ font.replace('.ttf','') }}</label>
        </div>
      </div>
    </div>

    <!-- ZIP Download -->
    <div v-if="selected.length" class="mt-4">
      <button
        @click="downloadZip"
        class="bg-green-500 text-white px-4 py-2 rounded"
      >
        ZIP herunterladen
      </button>
    </div>

  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      fonts: [],          // Alle Fonts vom Backend
      selected: [],       // Ausgewählte Fonts
      textInput: '',      // Textinput für Vorschau
      filtered: []        // Fonts nach Klick auf Filter
    }
  },
  computed: {
    // Fonts anzeigen: nur die gefilterten oder alle, wenn noch kein Filter
    displayedFonts() {
      return this.filtered.length ? this.filtered : this.fonts
    }
  },
  mounted() {
    this.loadFonts()
  },
  methods: {
    async loadFonts() {
      try {
        const res = await axios.get('/api/fonts')
        this.fonts = res.data
      } catch (e) {
        console.error('Fehler beim Laden der Fonts:', e)
      }
    },

    getImage(font) {
      // Live-Vorschau mit aktuellem textInput
      return `/toolz/image.php?file=${encodeURIComponent(font)}&text=${encodeURIComponent(this.textInput)}&t=${Date.now()}`
    },

    // Filter-Klick oben → nur die angehakten Fonts anzeigen
    applyFilter() {
      this.filtered = this.selected.slice() // nur die angehakten Fonts
    },

    resetSelection() {
      this.selected = []
      this.filtered = []
    },

    async downloadZip() {
      if (!this.selected.length) return
      try {
        const res = await axios.post('/api/fonts/zip', { fonts: this.selected })
        window.location.href = res.data.url
      } catch (e) {
        console.error('Fehler beim Erstellen der ZIP:', e)
      }
    }
  }
}
</script>

<style scoped>
</style>
