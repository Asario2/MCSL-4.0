<template>
    <th
        @click="$emit('sort', field)"
        class="px-1 py-2 text-left cursor-pointer select-none whitespace-nowrap hover:bg-gray-200 dark:hover:bg-gray-700 transition"
    >
        <div class="flex items-center gap-1">

            <slot>{{ title }}</slot>

            <span
                v-if="sortKey === field"
                class="text-xs"
            >
                {{ sortDirection === 'asc' ? '▲' : '▼' }}
            </span>

            <span
                v-else
                class="text-xs opacity-30"
            >
                ⇅
            </span>

        </div>
    </th>
</template>

<script>
export default {
    name: "SortableHeader",

    props: {
        field: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        sortKey: {
            type: String,
            default: "",
        },
        sortDirection: {
            type: String,
            default: "asc",
        },
    },

    emits: ["sort"],
};
</script>
