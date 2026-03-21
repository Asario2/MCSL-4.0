<template>
<img :src="'/images/_chh/web/new-summer-2022-9.jpg'" style='min-width:100%'>
<nav>
    <div class='nb text-center p-4'><span class="p-5 mb-4"><a class='navbtn' href='/visitcard' >Visitenkarte</a></span><span class='p-5 mb-4'><a class='navbtn' href='/publikationen' >Publikationen</a></span><span class='p-5 mb-4'><a class='navbtn' href='/home/privacy' >Datenschutzerklärung  </a></span></div>
</nav>
 <header
    v-if="$slots.header"
    class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800 mb-4 border-b border-layout-sun-100 dark:border-layout-night-100"
>
    <div class="max-w-7xl mx-auto py-0 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
    </div>
</header>
<main>
    <div class="min-h-screen">
        <div class="w-full max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Toast -->
            <div>
                <toast></toast>
            </div>

            <!-- Slot für Content -->
            <div class="mt-4">
                <div class='row whte_nopar'>
                    <div class='whte2'>
                    <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</template>
<script></script>
<style>
a.navbtn:link,a.navbtn:visited
{
    text-shadow:none;
    color:#222255;
    text-decoration: none;

}
NAV > DIV.nb{
    background-color:#ccc;
}
</style>
<!--  -->
