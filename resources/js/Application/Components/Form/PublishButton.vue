<template>
  <div>
    <!-- Veröffentlicht -->
    <button v-if="isPublished" @click="togglePub" title="Veröffentlicht" :disabled="loading">
      <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
        <circle cx="12" cy="12" r="10"/>
      </svg>
    </button>

    <!-- Unveröffentlicht -->
    <button v-else @click="togglePub" title="Unveröffentlicht" :disabled="loading">
      <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
        <circle cx="12" cy="12" r="10"/>
      </svg>
    </button>
  </div>
</template>

<script>
import axios from "axios";
import { route } from "ziggy-js";

export default {
  name: "PublishButton",
props: {
  table: String,
  id: Number,
  modelValue: { type: Boolean, default: false }, // v-model
  public: { type: Number, default: 1 }
},
computed: {
  isPublished: {
    get() { return this.modelValue; },
    set(val) { this.$emit('update:modelValue', val); }
  }
},
  data() {
    return {
    //   isPublished: this.published,
      loading: false,
    };
  },
  watch: {
    // Reaktivität: Prop-Änderungen vom Parent werden übernommen
    published(newVal) {
      this.isPublished = newVal;
    }
  },
  methods: {
    async togglePub() {
      if (this.loading) return;

      this.loading = true;
      const newStatus = this.isPublished ? 0 : 1;

      try {
        await axios.post(route("toggle.pub"), {
          table: this.table,
          id: this.id,
          pub: newStatus,
          public: this.public,
        });

        this.isPublished = newStatus; // UI sofort aktualisieren
      } catch (error) {
        console.error("Fehler beim Speichern:", error.response?.data || error.message);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
