<template>
    <div class="flex gap-2 flex-wrap">
        <a :href="facebook" target="_blank" class="share-btn bg-blue-600">
            <IconFacebook class="w-5 h-5"/>
        </a>

        <a :href="twitter" target="_blank" class="share-btn bg-black">
            <IconX class="w-5 h-5"/>
        </a>

        <a :href="telegram" target="_blank" class="share-btn bg-sky-500">
            <IconTelegram class="w-5 h-5"/>
        </a>

        <a :href="whatsapp" target="_blank" class="share-btn bg-green-500">
            <IconWhatsapp class="w-5 h-5"/>
        </a>

        <button @click="copyLink" class="share-btn bg-gray-700">
            <IconLink class="w-5 h-5"/>
        </button>

        <button
            v-if="navigatorShare"
            @click="nativeShare"
            class="share-btn bg-red-600">
            <IconShare class="w-5 h-5"/>
        </button>
    </div>
</template>

    <script>
    import { nextTick } from 'vue';
    import { parse } from 'url';
    import IconShare from "@/Application/Components/Icons/IconShare.vue";
    import IconWhatsapp from "@/Application/Components/Icons/IconWhatsapp.vue";
    import IconTelegram from "@/Application/Components/Icons/IconTelegram.vue";
    import IconFacebook from "@/Application/Components/Icons/IconFacebook.vue";
    import IconX from "@/Application/Components/Icons/IconX.vue";
    import { faL } from '@fortawesome/free-solid-svg-icons';
    export default {
        components: {
                IconShare,
                IconX,
                IconTelegram,
                IconWhatsapp,
                IconFacebook,

            },
        data() {
        return {
            showShareBox: false,
        };
        },
        props:{
            added:{
                type:String,
            },
            sse:{
                type:String,
                required:false,
            },
            url:String,
            title:String
},
mounted(){
    this.initShariff();
},

        methods: {
            toggleShareBox() {
                // console.log("toggleShareBox aufgerufen");

                this.showShareBox = !this.showShareBox;
                if (this.showShareBox) {
                this.initShariff();
                }
            },
                initShariff(){
        nextTick(()=>{
            const el=this.$refs.shariff;
            if(!el) return;

            el.setAttribute("data-url",this.url);

            new window.Shariff(el,{
                services:["facebook","telegram","whatsapp","twitter"],
                theme:"classic",
                orientation:"horizontal"
            });
        });
    }


    },

    };
    </script>

