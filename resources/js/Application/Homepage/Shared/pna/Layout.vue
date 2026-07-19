<template>

    <meta-header :title="headerTitle">
      <template #robots>

        <meta head-key="robots" name="robots" content="index, follow"/>
      </template>

      <template #description>
        <meta head-key="description" name="description" :content="headerDescription" />
        <!-- <link rel="canonical" v-if="headerUrl" :href="headerUrl" /> -->
      </template>

      <template #opengraph>
        <meta head-key="og:title" property="og:title" :content="headerTitle" />
        <meta property="og:description" head-key="og:description" :content="headerDescription" />
        <meta v-if="headerUrl" head-key="og:url" property="og:url" :content="headerUrl" />
        <meta v-if="headerImage" head-key="og:image" property="og:image" :content="headerImage" />
        <meta head-key="og:type" property="og:type" content="website" />
      </template>
    </meta-header>

    <main id="app-layout-start">
      <section class="relative bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 transition-colors duration-1000"  style='z-index:50;'>
        <!-- Header -->
        <nav class="fixed top-0 left-0 right-0 z-30 bg-layout-sun-50 dark:bg-layout-night-00 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 border-b border-layout-sun-2000 dark:border-layout-night-1060 overflow-visible  "  style='z-index:50;'>

          <div class="mx-auto w-fit px-6" style='z-index:50;'>
            <div class="flex items-center justify-between py-4 lg:flex-col lg:justify-center lg:gap-4">
                <!-- Logo -->
                <a href="/" class="">
                    <ClientOnly>
                        <pnalogo :mode="mode"/>
                    </ClientOnly>
                </a>

                <!-- Hamburger nur Mobile -->
                <button
                    @click="toggleNavbar"
                    type="button"
                    class="lg:hidden p-2 focus:outline-none  hover:text-primary-sun-800 dark:text-primary-night-1000 colored_white"
                    aria-label="Menü öffnen"
                >
                    <IconMenu class="w-7 h-7" v-if="!isOpen_Menu" />
                    <IconClose class="w-7 h-7" v-else />
                </button>
            </div>


            <!-- Mobile Menu open: "block", Menu closed: "hidden" -->
            <div :class="[isOpen_Menu ? 'translate-x-0 opacity-100 ' : 'opacity-0 -translate-x-full']" style='z-index:10000000;' class="absolute inset-x-0 mt-[6] w-full px-6 py-4 shadow-md transition-all duration-300 ease-in-out bg-layout-sun-00  dark:bg-layout-night-00  lg:relative lg:top-0 lg:mt-0 lg:flex lg:w-auto lg:translate-x-0 lg:items-center  lg:p-0 lg:opacity-100 lg:shadow-none lg:dark:bg-transparent">
            <div class="flex flex-col items-center space-y-1 SDAA
                lg:mt-4
                lg:flex-row
                lg:items-center
                lg:w-fit
                lg:mx-auto
                lg:justify-center
                lg:gap-2
                lg:space-y-0
                border-4 border-layout-sun-2000
                dark:border-layout-night-2000
                lg:rounded-lg
                mb-2
            ">
                <link-header_mfx class="ml-[26px]"   :route-name="route('home.index')" name="Home"></link-header_mfx>
                <link-header_mfx :route-name="route('home.pna.grafitti')" name="Grafittis"></link-header_mfx>
                <!--<link-header_mfx :route-name="route('home.pricing')" name="Preise"></link-header_mfx>-->
                <!-- <link-header_mfx :route-name="route('home.blog.index')" name="Blog"></link-header_mfx> -->
                <link-header_mfx :route-name="route('home.pna.portraits')" name="Portraits"></link-header_mfx>
                <link-header_mfx :route-name="route('home.pna.contacts')" name="Kontakt"></link-header_mfx>
                <button-change-mode :mode="mode" @changeMode="changeMode"></button-change-mode>
                <template v-if="!$page.props.userdata.user_id">
                  <link-header_mfx :route-name="route('login')" name="Login"></link-header_mfx>
                </template>
                <div v-else-if="$page.props.auth.user" class="block md:hidden">

                <link-header_mfx :route-name="route('admin.dashboard')" name="Dashboard" /><br />
                <link-header_mfx :route-name="route('admin.profile')" name="Profil">
                <img
                    id="prof_pic"
                    class="h-8 w-8 rounded-full object-cover mr-[4px] pr-[4px]"
                    :src="imagebasepath($page.props.auth.user?.profile_photo_url) +
                                                $page.props.auth.user?.profile_photo_url.replace('public','').replace('http://localhost/images/','').replace('images/images/','images/') || '/images/profile-photos/008.jpg'
                                                    "
                    :alt="$page.props.userdata.full_name"
                /></link-header_mfx><br />
                <hr />
                <form @submit.prevent="logoutUser">
                    <button type="submit">
                        <dropdown-link>
                            <b>Abmelden</b>
                        </dropdown-link>
                    </button>
                </form>
                </div>
                <template v-if="$page.props.userdata.user_id">
                  <!-- <link-header_mfx :route-name="route('applicationswitch')" name="Dashboard"></link-header_mfx> -->
                </template>

               <!-- <button-change-mode :mode="mode" @changeMode="changeMode"></button-change-mode>-->


                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                    />
                                                    <div class="ms-3 relative flex hidden sm:flex">
                                <Dropdown align="right" width="72" v-if="$page.props.auth.user" class="">
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-4 border-transparent rounded-full focus:outline-none focus:border-layout-sun-300 dark:focus:border-layout-night-300 transition"
                                        >
                                            <img
                                            id="prof_pic"
                                            class="h-8 w-8 rounded-full object-cover mr-6"

                                               :src="imagebasepath($page.props.auth.user.profile_photo_url) +  $page.props.auth.user.profile_photo_url.replace('public','')
                                                "
                                                :alt="
                                                    $page.props.userdata
                                                        .full_name
                                                "

                                            />
                                            {{  }}
                                        </button>

                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-layout-sun-500 dark:text-layout-night-500 bg-layout-sun-0 dark:bg-layout-night-0 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.userdata
                                                        .full_name
                                                }}

                                                <!-- <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5">
                                                </svg> -->
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Anwendung wechseln bzw. zur Startseite -->
                                        <div
                                            class="block px-4 py-2 text-xs text-layout-sun-500 dark:text-layout-night-500"
                                        >
                                            <span
                                                v-if="
                                                    $page.props.userdata
                                                        .application_count > 100
                                                "
                                                >Anwendung wechseln</span
                                            >
                                            <span v-else>Startseite</span>
                                        </div>
                                        <dropdown-link
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="
                                                route('admin.dashboard')
                                            "
                                        >
                                            <span
                                                v-if="
                                                    $page.props.userdata
                                                        .application_count > 100
                                                "
                                                >Anwendung wechseln</span
                                            >
                                            <span v-else>zum Dashboard</span>
                                        </dropdown-link>

                                        <!-- Account Management -->
                                        <div
                                            class="block px-4 py-2 text-xs text-layout-sun-500 dark:text-layout-night-500"
                                        >
                                            Dein Konto
                                        </div>

                                        <dropdown-link
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="route('admin.profile')"
                                        >
                                            Profil
                                        </dropdown-link>

                                    <div
                                            class="my-2 border-t border-layout-sun-200 dark:border-layout-night-1050"
                                        />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logoutUser">
                                            <button type="submit">
                                                <dropdown-link>
                                                    <b>Abmelden</b>
                                                </dropdown-link>
                                            </button>
                                        </form>
                                    </template>
                                </Dropdown>
                                </div>
              </div>
            </div>
          </div>
        </nav>

        <!-- Loading -->
        <!-- <div v-if="isLoading || loadingStore.isLoading" id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all" style='z-index:999999999'>
        <div class="text-center">
            <svg class="animate-spin h-10 w-10 text-primary-sun-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-4 text-primary-sun-100 text-sm">Bitte warten...</p>
        </div>
        </div> -->
        <Loader />
        <!-- Content -->
        <div class="container mx-auto max-w-6xl min-h-screen px-2 pm_top">
          <!-- Toast -->
          <div>
            <Toast></Toast>
          </div>

          <!-- Slot für Content -->
          <div>
            <slot></slot>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 border-t border-layout-sun-200 dark:border-layout-night-200" aria-labelledby="footer-heading">
        <div class="container mx-auto max-w-6xl">
          <h2 id="footer-heading" class="sr-only">Footer</h2>
          <div class="px-1 md:px-4 lg:px-8 pb-8 pt-8">
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 xl:col-span-2 xl:mt-0">
              <div class="md:grid md:grid-cols-2 md:gap-4">
                <div class="text-center md:text-left">
                  <h3 class="text-sm font-semibold leading-6 px-2">
                    <span> Webseite </span>
                  </h3>
                  <ul role="list" class="mt-6 space-y-4 list-none">
                    <li>
                      <link-footer name="Impressum" :route-name="route('home.imprint')"></link-footer>
                    </li>
                    <li>
                      <link-footer name="Datenschutzerklärung" :route-name="route('home.privacy')"></link-footer>
                    </li>
                    <li>
                            <a
                                class="ToggleCookieLink cursor-pointer inline-flex items-center gap-2
                                        rounded-lg px-2 py-1 text-sm
                                        text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900
                                        dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900"

                                        onclick="showHideToggleCookiePreferencesModal()"
                                >
                                <IconCookies width="18" height="18" class="mr-[-4px]" color="#e8c456"/>
                                <span>Cookie Einstellungen</span>
                                </a>
                    </li>
                  </ul>
                </div>
                <div class="text-center md:text-left">
                  <h3 class="text-sm font-semibold leading-6 px-2">
                    <span> Authentifizierung </span>
                  </h3>
                  <ul role="list" class="mt-6 space-y-4 list-none">
                    <li>
                      <link-footer name="Login" :route-name="route('login')"></link-footer>
                    </li>
                    <li v-if="SD() == 'ab'">
                      <link-footer  name="Registrierung" :route-name="route('register')"></link-footer>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="pt-8 text-layout-sun-700 dark:text-layout-night-700">
              <div class="flex flex-col items-center justify-between text-xs leading-5 gap-4">
                <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                  <div>
                    <brand-footer></brand-footer>
                  </div>
                  <div>
                    <!-- <link-footer>
                      <a href="https://www.facebook.de" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                        <icon-facebook class="flex-shrink-0 w-6 h-6"></icon-facebook>
                      </a>
                    </link-footer>
                    <link-footer>
                      <a href="https://www.xing.de" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                        <icon-xing class="flex-shrink-0 w-6 h-6"></icon-xing>
                      </a>
                    </link-footer>
                    <link-footer>
                      <a href="https://whatsapp.com " target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                        <icon-whatsapp class="flex-shrink-0 w-6 h-6"></icon-whatsapp>
                      </a>
                    </link-footer> -->
                  </div>
                </div>

                <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                  <div class="text-xs leading-6">
                      &copy; {{ year }} Starter Eleven/MCSL. Ein Template von <b>Oliver Reinking</b> / <b>Asario</b>.
                  </div>

                  <div class="text-xs leading-6">
                    <span><IconMCSL></IconMCSL> Version: </span>
                    {{ $page.props.version.versionnr }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </main>
  </template>
<script>
// import axios from "axios";
// import { router } from "@inertiajs/vue3";
// import { useLoadingStore } from "@/loading";
import pnalogo from "@/Application/Shared/pnalogo.vue";
import IconClose from "@/Application/Components/Icons/Close.vue";
import ClientOnly from "@/Application/Components/ClientOnly.vue"
import IconMCSL from "@/Application/Components/Icons/IconMCSL.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
// import BrandHeader from "@/Application/Shared/BrandHeader.vue";
import Dropdown from "@/Application/Components/Content/Dropdown.vue";
import DropdownLink from "@/Application/Components/Content/DropdownLink.vue";
import LinkHeader_mfx from "@/Application/Shared/LinkHeader_mfx.vue";
import BrandFooter from "@/Application/Shared/BrandFooter.vue";
import LinkFooter from "@/Application/Shared/LinkFooter.vue";
import IconMenu from "@/Application/Components/Icons/Menu.vue";
import Toast from "@/Application/Components/Content/Toast.vue";
// import ButtonChangeMode from "@/Application/Components/ButtonChangeMode.vue";
import { SD,showHideToggleCookiePreferencesModal } from "@/helpers";
import Loader from "@/Application/Components/Loader.vue";
import ButtonChangeMode from "@/Application/Components/ButtonChangeMode.vue";
// import { ref } from "vue";

export default {
  name: "Homepage_Shared_Layout_mfx",

  components: {
    MetaHeader,
    // BrandHeader,
    LinkHeader_mfx,
    BrandFooter,
    ButtonChangeMode,
    Loader,
    LinkFooter,
    Toast,
    IconMenu,
    IconMCSL,
    IconClose,
    pnalogo,
    Dropdown,
    DropdownLink,
    ClientOnly,
    // ButtonChangeMode,

  },

  props: {
    sd: {
      type: String,
      required: false,
    },
  },

//   setup() {
//     const loadingStore = useLoadingStore();
//     return { loadingStore };
//   },

  data() {
    return {

        headerDescription: this.$page?.props?.description ?? "",
        headerUrl: this.$page?.props?.url ?? null,
        headerImage: this.$page?.props?.image ?? null,

        isOpen_Menu: false,
        year: new Date().getFullYear(),
        // mode: (() => {

        //     if (typeof window === "undefined") {
        //         return 'dark';
        //     }

        //     const savedTheme = localStorage.getItem('theme');

        //     return savedTheme || 'dark';

        // })(),
        mode: (() => {
            const savedTheme = localStorage.getItem("theme");
            console.log("INIT THEME:", savedTheme);
            return savedTheme || "dark";
        })(),
    //   isLoading: localStorage.getItem("loading") === "true",
      search: "",
      isLoading: true,
      searchval: false,
      imagesLoaded: false,
      searchTimeout: null,
         headerTitle: this.$page?.props?.title ?? "",
    };
  },

mounted() {
        if (typeof window === "undefined") return;
        console.log(window.LaravelCookieConsent);
console.log(document.cookie);

    this.mode = localStorage.getItem("theme") || "dark";

    if (localStorage.getItem("mreload") === "true") {
        localStorage.setItem("mreload", "false");
        this.$nextTick(() => location.reload());
        return;
    }

    this.applyTheme();

//   this.headerTitle = this.$page?.props?.title ?? "";

//     if (typeof window !== "undefined") {
//   this.mode = localStorage.theme || 'dark';
// }
    // SSR-Schutz (wichtig!)
    //  localStorage.theme = this.mode;
    if (typeof window === "undefined") return;



    // =============================================
    // URL Parameter
    // =============================================
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get("search");
    this.search = searchParam ?? "";

    if (searchParam === "" || searchParam === null) {
        this.setLoadingState(true);
        this.searchval = true;
        this.startSearchTimeout();
    } else {
        this.setLoadingState(false);
        this.searchval = false;
    }

    // =============================================
    // Bilder beobachten (SSR-safe)
    // =============================================
    this.waitForImagesToLoad();

    // =============================================
    // LocalStorage
    // =============================================
    if (this.isLoading) {
        localStorage.setItem("loading", "true");
    }
},

  methods: {
    SD,

showHideToggleCookiePreferencesModal,
    applyTheme() {
        console.log("COMPONENT UID:", this._.uid);
        console.log("PATH:", window.location.pathname);
        console.log("MODE:", this.mode);
        const html =
            document.documentElement;

        console.log(
            "[applyTheme] mode:",
            this.mode
        );

        console.log(
            "[applyTheme] vorher:",
            html.className
        );

        /*
        |--------------------------------------------------------------------------
        | Force Light
        |--------------------------------------------------------------------------
        */

        const forceLight =
            window.location.pathname === '/login'
            || window.location.pathname === '/register';

        if (forceLight) {

            console.log(
                "[applyTheme] forceLight aktiv"
            );

            html.classList.remove('dark');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Theme anwenden
        |--------------------------------------------------------------------------
        */

        if (this.mode === 'dark') {

            console.log(
                "[applyTheme] ADD DARK"
            );

            html.classList.add('dark');

        } else {

            console.log(
                "[applyTheme] REMOVE DARK"
            );

            html.classList.remove('dark');
        }

        console.log(
            "[applyTheme] nachher:",
            html.className
        );
    },

//    changeMode(newMode) {

//     this.mode = newMode;
//     if(typeof newMode === "undefined")
//     {
//         newMode = 'dark';
//     }
//     const forceLight =
//         window.location.pathname === '/login'
//         || window.location.pathname === '/register';

//     if (!forceLight) {
//         localStorage.setItem('theme', newMode);
//     }

//     this.applyTheme();
// },
changeMode(newMode) {
 console.trace("changeMode", newMode);

    this.mode = newMode ?? (this.mode === 'dark' ? 'light' : 'dark');

    localStorage.setItem('theme', this.mode);

    this.applyTheme();
},
    imagebasepath(str){
        if(str.includes("https://"))
        {
            return ''
        }

        return `/images/_${SD()}/users/profile_photo_path/`
    },
    setLoadingState(state) {
//       console.log("🔄 setLoadingState:", state);
      this.isLoading = state;
    if(typeof window !== "undefined")
    {
      localStorage.setItem("loading",  state ? state.toString():'');
    }
},

    reopenCookieBanner() {

      if (typeof window !== "undefined" && window.LaravelCookieConsent){
        window.LaravelCookieConsent.reset();
      }
    },

    checkLoadingState() {
// //       console.log("🔍 checkLoadingState()", {
//         pending: this.pendingRequests,
//         imagesLoaded: this.imagesLoaded,
//         isLoading: this.isLoading,
//       });

     if (this.imagesLoaded) {
    this.setLoadingState(false);
  }
    },

    waitForImagesToLoad() {
  if (typeof document === "undefined") {
    this.imagesLoaded = true;
    return;
  }

  const images = document.querySelectorAll("img");
  const totalImages = images.length;

  if (totalImages === 0) {
    this.imagesLoaded = true;
    this.checkLoadingState();
    return;
  }

  let loaded = 0;

  const done = () => {
    loaded++;
    if (loaded === totalImages) {
      this.imagesLoaded = true;
      this.checkLoadingState();
    }
  };

  images.forEach(img => {
    if (img.complete) {
      done();
    } else {
      img.addEventListener("load", done);
      img.addEventListener("error", done);
    }
  });
},

    toggleNavbar() {
      this.isOpen_Menu = !this.isOpen_Menu;
    },
    logoutUser() {
      this.$inertia.post(this.route("logout"));
    },

    startSearchTimeout() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        if (this.search.trim() !== "") {
          this.setLoadingState(true);
        }
      }, 3000);
    },

    onSearchInput(event) {
      this.search = event.target.value;
      this.startSearchTimeout();
    },
  },
};
</script>

<style  >
#prof_pic{
    width:32px !important;
    height:32px !important;
    object-fit: cover;
}
.border-layout-night-x
{
border-color:#1f2937;
}
@media (min-width: 1023px){
.trans{
    /* background: linear-gradient(to right, #1a0a00, #1a0a00, #4d3213); */

}
}
.trans2{
    background-color:rgba(0,0,0) !important;
}
.pm_top{
    margin-top:85px;
}
@media (min-width: 1023px){
.pm_top{
padding-top:200px !important;
margin-top:150px !important;
}

}
.pad{
 font-size:20px;
 font-family:Helvetica !important;
 margin-bottom:2px;
 padding:3px !important
}
.dark .ddbg{
    background-color: rgb(3 7 18) !important;
}
.pna .colored_white{
    color:#F00 !important;
}
</style>

