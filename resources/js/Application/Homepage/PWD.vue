<template>
<div class="max-w-4xl mx-auto">

    <div class="rounded-lg border border-primary-sun-500 dark:border-primary-night-500 bg-layout-sun-0 dark:bg-layout-night-0 p-6">

        <h1 class="text-2xl font-bold mb-6 text-layout-sun-900 dark:text-layout-night-900">
            Passwort Hash Generator
        </h1>

        <InputFormText
            id="password"
            name="password"
            v-model="form.password"
            type="password"
            autocomplete="new-password"
        >
            <template #label>
                Passwort
            </template>
        </InputFormText>

        <button
            class="mt-4 px-5 py-2 rounded-lg bg-primary-sun-500 dark:bg-primary-night-500 text-white"
            @click="generateHash"
        >
            Hash erzeugen
        </button>

        <div v-if="hash" class="mt-6">

            <label class="block mb-2 text-sm font-medium text-layout-sun-900 dark:text-layout-night-900">
                Laravel Passwort-Hash
            </label>

            <textarea
                readonly
                rows="5"
                :value="hash"
                class="w-full rounded-lg border border-primary-sun-500 dark:border-primary-night-500 bg-layout-sun-100 dark:bg-layout-night-100 p-3 font-mono text-sm"
            ></textarea>

        </div>

    </div>

</div>
</template>

<script>
import axios from "axios";
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import { route } from 'ziggy-js';
export default {

    components:{
        InputFormText
    },

    data(){
        return{
            form:{
                password:""
            },
            hash:""
        };
    },

    methods:{

        async generateHash() {

                console.log("Button geklickt");
            try {

                const response = await axios.post('/pwd', this.form);

                console.log("Status:", response.status);
                console.log("Data:", response.data);

                this.hash = response.data.hash;

            } catch (e) {

                console.error("FEHLER:");

                console.log(e);

                if (e.response) {
                    console.log("Status:", e.response.status);
                    console.log("Response:", e.response.data);
                }

            }

        }

    }

}
</script>
