<template>
    <div class="w-full flex flex-wrap items-left gap-2">
        <slot></slot>
    </div>
</template>
<script>
import { CleanTable, CleanId } from '@/helpers';
import { route } from 'ziggy-js'
const table = CleanTable();
export default {
    name: "Contents_Form_ButtonGroup",

    props: {
        align: {
            type: String,
            default: "justify-center md:justify-end",
        },
        routeCreate: {
        type: String,
        default: () => '#',
    },
    },
    computed: {
    createUrl() {
        return route('admin.tables.create', this.table)
    }
}
};
</script>

