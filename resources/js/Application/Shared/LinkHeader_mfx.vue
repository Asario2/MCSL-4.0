<template>
<template v-if="routeName">
    <a
        class="pad cursor-pointer tracking-wide inline-flex items-center gap-2 rounded-lg px-2 py-2 text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000"
        :href="routeName"
        style="font-family:CuttingCorners;"
    >
        <slot></slot>
        <span>{{ name }}</span>
    </a>
</template>
    <template v-else>
        <div
            class="cursor-pointer tracking-wide inline-flex items-center gap-2 rounded-lg px-3 py-3 text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000"
            style="font-family:CuttingCorners;"
        >
            <slot></slot>
        </div>
    </template>
</template>

<script>
//
export default {
    name: "LinkHeaderMfx",
    //
    props: {
        routeName: {
            type: String,
            default: "home",
        },
        name: {
            type: String,
            default: "MSCL",
        },
        imgsrc:{
            type:String,
            default:'',
        },
        ialt:{
            type:String,
            default:'',
        },
        img:{
            type:String,
            default:'',
        },
    },
};
</script>

