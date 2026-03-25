<template>
  <Layout>
    <MetaHeader title="Zugriffs-Statistik" />
    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs" :current="'Zugriffs-Statistik'"></breadcrumb>
    </template>

    <div class="flex justify-between items-center mb-4">
      <!-- Linker Bereich -->
      <div class="flex items-center">
        <h2 class="text-xl font-bold">Seitenaufrufe pro URL</h2>
      </div>

      <!-- Rechter Bereich -->
      <div class="flex items-center space-x-4">
        <!-- Zeitraum -->
        <div class="flex items-center space-x-2">
          <span>Zeitraum</span>
          <select
            v-model="month"
            @change="loadData"
            class="p-2.5 text-sm rounded-lg border border-layout-sun-300
                   text-layout-sun-900 bg-layout-sun-50
                   focus:ring-primary-sun-500 focus:border-primary-sun-500
                   dark:border-layout-night-300 dark:text-layout-night-900
                   dark:bg-layout-night-50 dark:placeholder-layout-night-400
                   dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
          >
            <option value="1">1 Monat</option>
            <option value="2">2 Monate &nbsp;&nbsp;&nbsp;&nbsp;</option>
            <option value="3">3 Monate</option>
            <option value="4">4 Monate</option>
            <option value="5">5 Monate</option>
          </select>
        </div>

        <!-- Domain -->
        <div class="flex items-center space-x-2" v-if="modulRights?.StatisticsAll">
          <span>Domain(s)</span>
          <select
            v-model="dom"
            @change="loadData"
            class="p-2.5 pr-6 text-sm rounded-lg border border-layout-sun-300
                   text-layout-sun-900 bg-layout-sun-50
                   focus:ring-primary-sun-500 focus:border-primary-sun-500
                   dark:border-layout-night-300 dark:text-layout-night-900
                   dark:bg-layout-night-50 dark:placeholder-layout-night-400
                   dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
          >
            <option value="all" selected>Alle Domains</option>
            <option value="ab">Asarios Blog</option>
            <option value="chh">Christian Henning</option>
            <option value="mfx">MarbleFX</option>
            <option value="dag">Monika Dargies&nbsp;&nbsp;</option>
          </select>
        </div>
      </div>
    </div>

    <canvas ref="canvas" style="max-height:500px;"></canvas>

    <!-- Neue Liste mit Links + Delete -->
    <div class="mt-6">
      <h3 class="text-lg font-semibold mb-2">URLs Übersicht</h3>
      <div class="mt-6">
  <h3 class="text-lg font-semibold mb-3 text-layout-sun-900 dark:text-layout-night-900">
    URLs (mit Löschfunktion)
  </h3>

  <ul class="space-y-2 pr-1">

    <li
      v-for="(label, idx) in labels"
      :key="idx"
      class="group flex items-center justify-between
             p-3 rounded-xl border
             border-layout-sun-200 dark:border-layout-night-300
             bg-layout-sun-50 dark:bg-layout-night-50
             hover:bg-layout-sun-100 dark:hover:bg-layout-night-100
             transition"
    >

      <!-- Link + Index -->
      <div class="flex items-center space-x-3">

        <!-- Index Badge -->
        <span class="text-xs px-2 py-1 rounded-md
                     bg-layout-sun-200 text-layout-sun-800
                     dark:bg-layout-night-200 dark:text-layout-night-800">
          {{ idx + 1 }}
        </span>

        <!-- URL -->
        <a
          :href="label"
          target="_blank"
          class="truncate max-w-[400px]
                 text-blue-600 dark:text-blue-400
                 hover:underline"
        >
          {{ label }}
        </a>

      </div>

      <!-- Actions -->
      <div class="flex items-center space-x-2">

        <!-- Open Icon -->
        <a
          :href="label"
          target="_blank"
          class="opacity-0 group-hover:opacity-100 transition
                 text-gray-400 hover:text-blue-500"
          title="Öffnen"
        >
          🔗
        </a>

        <!-- Delete Button -->
        <button
          @click="deleteLabel(label, idx)"
          class="opacity-0 group-hover:opacity-100 transition
                 px-2 py-1 rounded-md
                 text-red-600 hover:text-white
                 hover:bg-red-600
                 dark:hover:bg-red-700"
          title="Löschen"
        >
          ✕
        </button>

      </div>
    </li>

  </ul>
</div>
    </div>
  </Layout>
</template>

<script>
import axios from "axios";
import { loadRights } from '@/helpers';
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
  Title
} from "chart.js";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import { SD } from "@/helpers";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend, Title);

export default {
  name: "PageViewsChart",
  components: { Layout, Breadcrumb, MetaHeader },

  data() {
    return {
      chart: null,
      dom: "all",       // Domain-Auswahl
      save:false,
      month: '1',
      modulRights: null,
      labels: [],    // URLs für die Liste
    };
  },

  async mounted() {
    this.loadData();
    this.modulRights = await loadRights();
  },

  methods: {
    SD,

    async loadData() {
      try {
        if (!this.dom) this.dom = '';
        this.month = this.month ?? "1";

        const res = await axios.get("/dboard/data/" + this.dom + "/" + this.month);
        const payload = res.data;

        this.labels = payload.labels || [];
        this.renderChart(this.labels, payload.datasets || []);
      } catch (error) {
        console.error("Fehler beim Laden der Statistik:", error);
      }
    },

    async loadMonth() {
      return this.month;
    },

    renderChart(labels, datasets) {
      console.log('Labels:', labels);
      console.log('Datasets:', datasets);

      if (!datasets || datasets.length === 0) {
        datasets = [{
          label: 'Seitenaufrufe',
          data: labels.map(() => 0),
          backgroundColor: 'rgba(59, 130, 246, 0.5)',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 1
        }];
      }

      const ctx = this.$refs.canvas.getContext("2d");
      if (this.chart) this.chart.destroy();

      this.chart = new Chart(ctx, {
        type: "bar",
        data: { labels, datasets },
        options: {
          responsive: true,
          plugins: {
            legend: { position: "top" },
            title: { display: true, text: "Seitenaufrufe pro URL nach Domain" }
          },
          scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, precision: 0 }
          }
        }
      });
    },

   async deleteLabel(label, idx) {
    if (!confirm(`Möchten Sie die Statistik für "${label}" wirklich löschen?`)) return;

    let save = confirm(`Möchten Sie diesen Eintrag für immer entfernen?`);

    try {
        const res = await axios.post('/api/delete-stat', {
            url: label,
            dom: this.dom,
            save
        });

        if (res.data.success && res.data.deleted > 0) {
            // Lokales Entfernen
            this.labels.splice(idx, 1);

            // Optional: Chart neu rendern
            this.renderChart(this.labels, this.chart?.data?.datasets || []);
        } else {
            alert("Eintrag wurde nicht gefunden oder konnte nicht gelöscht werden.");
        }

    } catch (error) {
        console.error("Fehler beim Löschen:", error);
        alert("Fehler beim Löschen der Statistik.");
    }
},
  },

  beforeUnmount() {
    if (this.chart) this.chart.destroy();
  }
};
</script>

<style scoped>
canvas {
  height: 400px;
  width: 100%;
}
</style>
