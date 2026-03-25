<template>
<img :src="'/images/_chh/web/new-summer-2022-9.jpg'" class="mt-[-1px]" style='min-width:100%'>
<nav class="bg-gray-300 border-b">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center">


            <div class="hidden md:flex">
                <a
                class="navbtn p-4"
                href="/visitcard"
                :class="{ active: isActive('/visitcard') }"
                @mouseenter="hovered = '/visitcard'"
                @mouseleave="hovered = null"
                >
                Visitenkarte
                </a>

                <a
                class="navbtn p-4"
                href="/publikationen"
                :class="{ active: isActive('/publikationen') }"
                @mouseenter="hovered = '/publikationen'"
                @mouseleave="hovered = null"
                >
                Publikationen
                </a>

                <a
                class="navbtn p-4"
                href="/home/privacy"
                :class="{ active: isActive('/home/privacy') }"
                @mouseenter="hovered = '/home/privacy'"
                @mouseleave="hovered = null"
                >
                Datenschutzerklärung
                </a>

                <a
                class="navbtn p-4"
                href="/imprint"
                :class="{ active: isActive('/imprint') }"
                @mouseenter="hovered = '/imprint'"
                @mouseleave="hovered = null"
                >
                Impressum
                </a>
            </div>
            <a class="md:hidden navbtn_res p-4 block text-left" href="/visitcard">Visitenkarte</a>
            <a class="md:hidden navbtn_res p-4 block" href="/publikationen">Publikationen</a>
            <button @click="open = !open" class="md:hidden mr-4 text-xl">
                ☰
            </button>

        </div>
    </div>

    <div v-if="open" class="md:hidden px-4 pb-4 space-y-2">


    <a class="navbtn_res p-4 block" href="/home/privacy">Datenschutzerklärung</a>
    <a class="navbtn_res p-4 block" href="/imprint">Impressum</a>
    </div>
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






<div class="w-full">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 whte_nopar notop">

    <div id="footer_ch" class="text-right pr-2">
      <div class="flex flex-wrap md:flex-nowrap justify-end items-center gap-2 pr-2">

        <!-- Links -->
        <div class="flex flex-wrap items-center gap-2 justify-end">
          &copy; <span itemprop="copyrightYear">{{ year }}</span> by
          <a class="blk" href="http://www.asario.de/" target="_AsariosBlog">Asario</a> |
          <a href="javascript:void(0);" onclick="showHideToggleCookiePreferencesModal()">Cookieeinstellung</a>
        </div>
        |
        <!-- Powered by -->
        <div class="w-full md:w-auto flex justify-end">

          <a href="https://www.marblefx.de/powered-by-mcsl" target="_blank" class="flex items-center gap-1">
            Powered by
            <img class="h-5 mt-1" :src="'/images/icons/MCSL_sm.png'" />
          </a>
        </div>

      </div>
    </div>

  </div>

</div>
</div>
</div>
<br />
</main>

</template>
<script>
export default {
    name: "Admin_Shared_Layout",
data(){
return {
    year: new Date().getFullYear(),
    open:false,
    hovered: null

}
},
methods: {
 isActive(path) {
    // Wenn gerade ein anderer Link gehovert wird → kein active anzeigen
    if (this.hovered && this.hovered !== path) {
      return false;
    }

    // Normaler active check
    return window.location.pathname.startsWith(path);
  }
}
};
</script>
<style>
a.navbtn:link,a.navbtn:visited
{
    text-shadow:none;
    color:#222255;
    text-decoration: none;
    border:2px solid rgb(209 213 219);

}
a.navbtn:link:hover,a.navbtn:visited:hover,a.active:link,a.active:visited
{
    text-shadow:none;
    color:#eee;
    text-decoration: none;
    background-color: #002d62;
    margin-bottom:-1px;
    border:2px solid #fff;

}
a:link,a:visited{
    text-decoration: none;
}
NAV > DIV.nb{
    background-color:#ccc;
}
.whte_nopar{
    border-bottom-right-radius:0px;
    border-bottom-left-radius:0px;
}
.notop{
    border-top:3px solid #002d62 !important;
    border-top-right-radius:0px;
    border-top-left-radius:0px;
    border-bottom-right-radius:15px;
    border-bottom-left-radius:15px;
}
.whte_nopar
{
    min-width:100% !important;
    /* background-color:transparent !important; */
    border-color:transparent;
}
.footer_ch{
    border-top:2px solid navy !important;
}
</style>
<!--  -->
