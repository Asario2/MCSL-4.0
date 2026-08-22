<template>
    <Layout>
        <MetaHeader title="Willkommen auf Paul Nadlers Homepage" />

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 items-center lg:items-start">

            <!-- Bild -->
            <img
                :src="'/images/_pna/paulnadler.jpg'"
                alt="Paul Nadler"
                class="h-[630px] w-auto object-cover shrink-0 rounded-lg shadow-lg"
            >

            <!-- Text -->
            <div class="flex-1">
                <span v-html="data[0]?.text"></span>
            </div>

        </div>
    </Layout>
</template>
<script>
import Layout from "@/Application/Homepage/Shared/pna/Layout.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
export default{

    name: "Home_PNA",
    components:{
        Layout,
        MetaHeader,
    },
    props:
    {
        data:{
            type:[Array,Object]
        }
    }
};

</script>
