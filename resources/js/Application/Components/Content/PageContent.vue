<template>
    <div class="my-2        ">
        <div v-if="SD() != 'ab'" class="p-1 md:p-4">
            <slot name="content" />
        </div>
        <div v-else class="p-1 md:p-4 lg:rounded-lg">
            <slot name="content" />
        </div>
    </div>
</template>

<script>
import { SD } from "@/helpers";

export default {
    name: "Components_Content_PageContent",
    methods: {
        SD
    }
};
</script>

