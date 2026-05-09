<template>
    <div v-if="mounted">
        <slot />
    </div>
</template>

<script>
export default {
    name: "ClientOnly",
    data() {
        return {
            mounted: false,
        };
    },
    mounted() {
        this.mounted = true;
    },
};
</script>
