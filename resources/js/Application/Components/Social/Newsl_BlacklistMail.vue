<template>
<Layout>
    <MetaHeader title="Vom Newsletter abmelden" />
    <div class="flex flex-col items-center justify-center space-y-4 mt-[100px]">
        <input-container class="w-full">
        <InputFormText
            id="email"
            name="email"
            v-model="email"
            placeholder="Email Adresse"
            :required="true"
        >
        <template #label>Email Adresse</template>
        </InputFormText>
        </input-container>
        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded mt-4" @click="unsubscr()">Vom Newsletter abmelden</button>
      <div class="text-center text-lg font-semibold">

      </div>
    </div>
  </Layout>
</template>

<script>
import axios from "axios";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
export default {
components:{

    Layout,
    MetaHeader,
    InputFormText,
},
props: {
    xvalue: {
        type: [String, Number, null],
        default: null
    },
    modelValue: {
        type: [String, Number],
        default: ''
    }
},
data() {
    return {
        email: this.xvalue ?? ''
    }
},

methods:{
        async unsubscr(){
        try{
            const res = await axios.post('/unsubsc_mail',{
                email:this.email
            });

            console.log(res.data);
        }catch(e){
            console.error(e);
        }
    }
}
};

</script>

