<template>
  <Layout>
    <MetaHeader title="Dieser Newsletter wurde schon verpunktet" />
    <div class="flex flex-col items-center justify-center h-screen space-y-4 mt-[-80px]">
      <ErrorSVG color="#FFC900"></ErrorSVG>
      <div class="text-center text-lg font-semibold">
        Dieser Newsletter wurde schon verpunktet
      </div>
    </div>
  </Layout>
</template>

<script>
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import ErrorSVG from "@/Application/Components/Icons/ErrorSVG.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import { toastBus } from "@/utils/toastBus";
export default {
components:{
    ErrorSVG,
    Layout,
    MetaHeader,
},
props:{
    i:[String,Number],
},
mounted()
{
    window.toastBus.emit( { status: 'points', message: 'Du hast 8 MCSL Points gesammelt'});
}
};

</script>

