<template>
<Layout>
    <MetaHeader title="Willkommen auf Paul Nadlers Homepage"></MetaHeader>
    <span v-html="data[0]?.text"></span>
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
