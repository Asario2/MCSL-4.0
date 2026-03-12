<template>
        <div
      v-if="GalOpen"
      class="fixed inset-0 z-100 flex items-start justify-center bg-black bg-opacity-50 pt-[160px] overflow-y-auto mb-[50px]"
    >
    <div
        class="bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-lg w-full max-w-lg p-6 max-h-[calc(100vh-200px)]"
      >
    <div
      class="bg-white dark:bg-gray-900 p-4 rounded mx-auto shadow-md w-full max-w-4xl overflow-y-auto"
      :style="`max-height: ${windowHeight - 160}px`"
    >
      <div v-if="images.length > 0">
        <div
          v-for="(element, index) in images"
          :key="element.fileName"
          class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 py-3"
          draggable="true"
          @dragstart="onDragStart(index)"
          @dragover.prevent
          @drop="onDrop(index)"
        >
          <span class="text-sm text-gray-500 dark:text-gray-400 cursor-move">
            #{{ element.position }}
          </span>

          <span class="min-w-[100px] cursor-move">
            <img
              :src="`${localFolder}/thumbs/${element.fileName}`"
              :alt="element.fileName"
              class="max-w-[100px] max-h-[75px] rounded shadow"
              @error="handleImageError(element.fileName)"
            />
          </span>

          <input
            type="text"
            v-model="element.label"
            class="txt w-full p-2.5 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75
                   bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500
                   focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200
                   selection:text-layout-sun-1000 dark:bg-layout-night-0 dark:text-layout-night-900
                   dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500
                   placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"
          />

          <form @submit.prevent="deletePost(index)" style="display:inline">
            <button
              @click.stop
              type="submit"
              onclick="return confirm('Sind Sie sicher, dass Sie dieses Bild löschen möchten?');"
            >
              <IconTrash class="sm-pencil cursor-pointer mt-1" />
            </button>
          </form>
        </div>
      </div>

      <div v-else class="text-gray-500 dark:text-gray-400 text-center py-10">
        ❌ Keine Bilder vorhanden.
      </div>

     <div class="mt-6 flex justify-between">
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
        >
          Schliessen
        </button>

        <button
          @click="saveJson"
          class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow-md transition"
        >
          💾 Speichern
        </button>
      </div>
    </div>
    </div>
    </div>
  </template>

  <script>
  import axios from 'axios';
  import IconTrash from "@/Application/Components/Icons/Trash.vue";
  import { toastBus } from '@/utils/toastBus';
  import { SD } from "@/helpers";
  export default {
    name: 'ImageJsonEditor',
    components: { IconTrash },
    props: {
      folder: { type: String, required: true },
      column: { type: String, required: true },
      JsonPath: {type:String},
      isGalOpen: {type:[String,Number,Boolean]},
    },
    data() {
      return {
        images: [],
        windowHeight: window.innerHeight,
        draggedIndex: null,
        status: 'Not loaded',
        GalOpen: this.isGalOpen ?? false,
        localFolder: this.folder, // Kopie vom Prop

      };
    },
    watch: {
      folder: {
        immediate: true,
        handler(newFolder) {
//           console.log('🔄 Folder changed:', newFolder);
          this.fetchImages();
        }
      }
    },
    methods: {
        SD,
      updateWindowHeight() {
        this.windowHeight = window.innerHeight;
      },

      // Drag & Drop
      onDragStart(index) {
        this.draggedIndex = index;
      },
      closeModal(){
        this.$emit('close');
      },
      onDrop(dropIndex) {
        if (this.draggedIndex === null) return;
        const movedItem = this.images.splice(this.draggedIndex, 1)[0];
        this.images.splice(dropIndex, 0, movedItem);
        this.images.forEach((img, idx) => (img.position = idx + 1));
        this.draggedIndex = null;
      },

      // Bild löschen
      async deletePost(index) {
        // if (!confirm('Sind Sie sicher, dass Sie dieses Bild löschen möchten?')) return;
        //const image = this.images[index];

        try {
          const folderName = this.localFolder.replace(/\/+$/, '').split('/').pop();
          const response = await axios.post(`/api/del_image/${this.column}/${folderName}/${index}`);
          window.toastBus.emit(response.data);

          this.images.splice(index, 1);
          this.refreshGallery();
        } catch (err) {
          console.error('Fehler beim Löschen:', err);
          alert('Fehler beim Löschen');
        }
      },

      // JSON speichern
      async saveJson() {
        try {
          await axios.post('/api/save-json', { folder: this.localFolder, images: this.images });
          this.fetchImages();
          window.toastBus.emit( { status: 'success', message: 'Galerie gespeichert', action:'img_json' });
        } catch (err) {
          console.error(err);
          alert('Fehler beim Speichern');
        }
      },

      // TEST METHODE: JSON URL direkt testen
      async testJsonUrl() {
//         console.log('🧪 Testing JSON URL...');
        const testUrl = `${this.localFolder}/index.json`;
//         console.log('📡 Testing URL:', testUrl);

        try {
          const response = await fetch(testUrl);
//           console.log('📊 Response status:', response.status);
//           console.log('📊 Response ok:', response.ok);

          if (response.ok) {
            const data = await response.json();
//             console.log('✅ JSON Data received:', data);
            this.status = `✅ OK - ${data.length} images`;
          } else {
//             console.log('❌ Response not OK');
            this.status = `❌ Error: ${response.status}`;
          }
        } catch (error) {
          console.error('💥 Fetch error:', error);
          this.status = `💥 Fetch Error: ${error.message}`;
        }
      },

      // Bilder aus index.json laden
      async fetchImages() {
//         console.log('🚀 fetchImages() called');
//         console.log('📁 Folder:', this.localFolder);

        this.status = 'Loading...';

        try {
          // Cache Busting
          const timestamp = new Date().getTime();
          const url = `${this.localFolder}/index.json?t=${timestamp}`;
//           console.log('📡 Fetching from:', url);

          const response = await axios.get(url);
//           console.log('📨 Response received:', response);

          if (Array.isArray(response.data)) {
            this.images = response.data;
//             console.log(`✅ SUCCESS: ${this.images.length} images loaded`);
            this.status = `Loaded ${this.images.length} images`;

            // Debug: Log each image
            this.images.forEach((img, index) => {
//               console.log(`🖼️ ${index + 1}: ${img.fileName} (pos: ${img.position})`);
            });
          } else {
//             console.log('⚠️ Response is not an array:', response.data);
            this.images = [];
            this.status = 'No array data';
          }
        } catch (err) {
          console.error('💥 ERROR loading JSON:', err);
          console.error('💥 Error details:', err.response);
          this.images = [];
          this.status = `Error: ${err.message}`;
        }
      },

      handleImageError(fileName) {
//         console.log(`❌ Image failed to load: ${fileName}`);
      },

      // Vom Parent aufrufbar
      refreshGallery() {
//         console.log('🔄 refreshGallery() called from parent');
        this.fetchImages();
        this.$emit('refresh-gallery');
      }
    },
    mounted() {
        // this.localFolder = this.JsonPath;
//         console.log('🎯 ImageJsonEditor MOUNTED');
//       console.log('📁 Initial folder:', this.localFolder);
//       console.log('📝 Column:', this.column);



      this.fetchImages();
      this.updateWindowHeight();
      window.addEventListener('resize', this.updateWindowHeight);
    },
    beforeUnmount() {
      window.removeEventListener('resize', this.updateWindowHeight);
    }
  };
  </script>

