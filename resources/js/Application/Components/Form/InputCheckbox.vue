<template>
  <div class="flex items-center h-6">
    <div class="flex items-center h-6">

      <!-- Hidden enthält IMMER aktuellen Wert -->
      <input
        type="hidden"
        :name="name"
        :id="name + '_hidden'"
        :value="numericValue"
      />

      <!-- Checkbox nur für UI -->
      <input
        :id="name"
        type="checkbox"
        v-model="local"
        class="w-5 h-5 rounded border
               focus:ring-3
               bg-primary-sun-50 text-primary-sun-500
               border-primary-sun-300 focus:ring-primary-sun-300
               ring-offset-primary-sun-800 checked:bg-primary-sun-500
               dark:bg-primary-night-50 dark:text-primary-night-500
               dark:border-primary-night-300 dark:focus:ring-primary-night-300
               dark:ring-offset-primary-night-800
               dark:checked:bg-primary-night-500
               cursor-pointer"
      />

    </div>

    <div class="ml-3 text-sm">
      <label :for="name" class="cursor-pointer">
        <span v-if="label">{{ label }}</span>
        <span v-else><slot /></span>
        <icon-exclamation-circle
          v-if="helptext"
          class="inline-block ml-1 w-5 h-5"
        />
      </label>

      <tippy
        v-if="helptext"
        v-html="helptext"
        placement="left"
      />
    </div>
  </div>
</template>

<script>
import IconExclamationCircle from "@/Application/Components/Icons/ExclamationCircle.vue";

export default {
  name: "Contents_Form_InputCheckbox",
  components: { IconExclamationCircle },

  props: {
    name: { type: String, required: true },
    modelValue: { type: [Number, String, Boolean], default: 0 },
    label: { type: String, default: "" },
    helptext: { type: String, default: "" },
  },

  emits: ["update:modelValue"],

  computed: {
    // Boolean für Checkbox-UI
    local: {
      get() {
        return Number(this.modelValue) === 1
      },
      set(value) {
        this.$emit("update:modelValue", value ? 1 : 0)
      }
    },

    // Numerischer Wert für Hidden-Input
    numericValue() {
      return Number(this.modelValue) === 1 ? 1 : 0
    }
  }
};
</script>
