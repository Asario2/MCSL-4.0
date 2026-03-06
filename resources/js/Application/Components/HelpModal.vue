<template>
<div

        class="fixed inset-0 flex items-start justify-center bg-black bg-opacity-50 pt-[160px] overflow-y-auto mb-[50px]" style="z-index:1000;"
        >
        <div
            class="bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-lg w-full max-w-lg p-6 max-h-[calc(100vh-200px)]"
        >
        <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">{{ headline }}</h2>
        <button
        @click="closemodal"
        class="text-2xl font-bold hover:text-red-500 transition-colors duration-200"
        >&times;</button>
        </div>
         <div v-html="textbody"></div>
        </div>
        </div>
</template>
<script>
import { CleanTable } from '@/helpers';
import axios from "axios";
export default {
    name:"HelpModal",
  components: {

  },
  props: {
   info_type:String,

  },
  data(){
    return{
    QString:'',
    headline: "",
    textbody: "",
    table:'',
    }
  },
  methods:{
 closemodal() {
    this.$emit('close');
  },
  },
  mounted(){
    this.table = CleanTable();
    if (!this.info_type) return;
    axios.get('/api/gethelp/' + this.info_type + "/" + this.table)
      .then(response => {
        this.headline = response.data?.headline ?? "";
        this.textbody = response.data?.textbody ?? "";
        console.log(response);
      })
      .catch(error => {
        console.error(error);
      });
},

};
</script>
