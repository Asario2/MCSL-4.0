<template>
    <div>
      <label
        :for="id"

        class="w-full text-sm font-medium text-gray-700 dark:text-gray-300 !mt-4"
      >
        <slot name="label">Label</slot>
      </label>
      <input
        type="email"
        :id="id"
        :name="name"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="[
          'w-fully p-2.5 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75',
          'bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500 focus:ring-primary-sun-500 placeholder:text-layout-sun-400',
          'selection:bg-layout-sun-200 selection:text-layout-sun-1000',
          'dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400',
          'dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000',
          $attrs.class
        ]"
        v-bind="$attrs"
      />
    </div>
    <input type="hidden" :name="name" :id="name + '_alt'" :value="modelValue" />
    <input type="hidden" name="email_hash" id="email_hash" :value="email_hash" />
    </template>

  <script>
  import { sha256 } from "@/helpers";
  export default {
    name: 'InputEmSecure',

    inheritAttrs: false, // <-- Damit $attrs.class manuell verarbeitet werden kann

    props: {
      id: { type: String, required: true },
      name: { type: String, required: true },
      modelValue: { type: [String, Number], default: '' },
      placeholder: { type: String, default: '' },
      required: { type: [String, Boolean], default: '' },
      disabled: Boolean,
          type: {
        type: String,
        default: 'text'
    }
    },
    data() {
        return {
            email_hash: '',
        };
    },
    watch: {
    async modelValue(val) {
        this.email_hash = await this.sha256(val);
    }
},
    methods:
    {
        sha256,
    },
    async mounted() {
        this.email_hash =
            await this.sha256(
                this.modelValue || ''
            );
    }
  };
  </script>

  <style>
  .w-fully {
        min-width: 94%;
        max-width: 95%;
    }
    .words{
        max-width:15% !important;
    }
  </style>

