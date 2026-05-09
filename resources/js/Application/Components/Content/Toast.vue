<template>
  <!-- Toast Container -->
  <div>
    <transition-group name="toast" tag="div">

      <div
        v-for="toast in toastBus.toasts"
        :key="toast.id"
        v-show="toast.show"
        class="mb-2 flex items-center w-full p-2 rounded-lg shadow text-layout-sun-900 dark:text-layout-night-900 transition-all duration-300"
        :class="determineAlertClass(toast.type)"
        role="alert"
      >

        <!-- Content -->
        <div
          class="flex items-center w-full"
        >

          <!-- SUCCESS -->
          <div
            v-if="toast.type === 'success'"
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-text-green-500"
          >
            <icon-done class="w-5 h-5"></icon-done>
          </div>

          <!-- POINTS -->
          <div
            v-if="toast.type === 'points'"
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-text-yellow-1060"
          >
            <icon-done class="w-5 h-5"></icon-done>
          </div>

          <!-- INFO -->
          <div
            v-if="toast.type === 'info'"
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-text-blue-500"
          >
            <icon-done class="w-5 h-5"></icon-done>
          </div>

          <!-- WARNING -->
          <div
            v-if="toast.type === 'warning'"
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-orange-500"
          >
            <icon-exclamation class="w-5 h-5"></icon-exclamation>
          </div>

          <!-- ERROR -->
          <div
            v-if="toast.type === 'error'"
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-red-500"
          >
            <icon-exclamation class="w-5 h-5"></icon-exclamation>
          </div>

          <!-- MESSAGE -->
          <div class="ml-3 text-base font-normal flex-1">
            <span v-html="toast.message"></span>
          </div>

          <!-- CLOSE -->
          <button
            type="button"
            class="ml-auto rounded-full p-1.5 inline-flex h-8 w-8 focus:ring-2 hover:text-white hover:bg-gray-800 border border-gray-400"
            @click="removeToast(toast.id)"
          >
            <span class="sr-only">Close</span>
            <icon-close class="w-5 h-5"></icon-close>
          </button>

        </div>

      </div>

    </transition-group>
  </div>
</template>

<script>
import { watch } from "vue";
import { toastBus } from "@/utils/toastBus";
import { watchEffect } from "vue";
import IconDone from "@/Application/Components/Icons/Done.vue";
import IconExclamation from "@/Application/Components/Icons/Exclamation.vue";
import IconClose from "@/Application/Components/Icons/Close.vue";

export default {
name: "Toast",
components: { IconDone, IconExclamation, IconClose },

data() {
    return { toastBus };
},

mounted() {
// Debug (optional)

},

methods: {
    removeToast(id) {
    this.toastBus.removeToast(id);
    },
    determineAlertClass(type) {
    switch (type) {
        case "success":
            return "border border-green-200 dark:border-green-800";
        case "points":
            return "border border-yellow-500 dark:border-yellow-1060 dark:text-yellow-1060";
        case "warning":
            return "border border-orange-200 dark:border-orange-800";
        case "info":
            return "border border-sky-200 dark:border-sky-800";
        case "error":
            return "border border-red-200 dark:border-red-800";
        default:
            return "border border-layout-sun-200 dark:border-layout-night-200";
    }
    },
},
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
transition: all 0.2s ease;
}
.toast-enter-from,
.toast-leave-to {
opacity: 0;
transform: translateY(-10px);
}
</style>
