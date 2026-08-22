<template>
    <label :for="name">{{ label }}</label>

    <select
        :id="name"
        :name="name"
        v-model="selectedValue"
        @change="$emit('update:modelValue', selectedValue)"
        class="p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
    >
        <option disabled value="0">Bitte wählen</option>

        <option
            v-for="(option, index) in options"
            :key="index"
            :value="String(option.tag)"
        >
            {{ option.name }}
        </option>
    </select>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';

export default {
    name: 'CopyleftSelect',

    props: {
        modelValue: {
            type: String,
            default: '0'
        },
        label: String,
        name: String
    },

    emits: ['update:modelValue'],

    data() {
        return {
            options: [],
            user: [],
            selectedValue: '0'
        };
    },

    async mounted() {
        const [copyleftResponse, userResponse] = await Promise.all([
            axios.get("/copyleft/images"),
            axios.get('/GetUserNull')
        ]);

        this.options = copyleftResponse.data.copyleft || [];
        this.user = userResponse.data || [];

        if (this.options.length > 0) {
            this.selectedValue = String(this.options[0].tag);

            this.$emit(
                'update:modelValue',
                this.selectedValue
            );
        }
    }
};
</script>
