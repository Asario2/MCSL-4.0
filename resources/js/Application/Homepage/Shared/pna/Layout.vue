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

        <section class="relative bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-0 dark:text-layout-night-900 transition-colors duration-1000"  style='z-index:50;'>
            <!-- Header -->
<nav
    class="fixed top-0 left-0 right-0 z-30 bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-0 bbg dark:text-layout-night-900"
    style="z-index:50;"
>

    <!-- ========================================================= -->
    <!-- LOGO + HAMBURGER                                          -->
    <!-- ========================================================= -->

    <div class="container mx-auto max-w-6xl">

        <div class="flex items-center justify-between">

            <!-- LOGO -->
            <div class="flex-1 min-w-0">
                <PnaLogo />
            </div>

            <!-- HAMBURGER - nur Mobile -->
            <div class="flex lg:hidden shrink-0 ml-3 px-4">

                <button
                    @click="toggleNavbar()"
                    type="button"
                    class="focus:outline-none text-primary-sun-1000 dark:text-primary-night-1000"
                    aria-label="toggle menu"
                >

                    <icon-menu
                        class="w-6 h-6" color="#f00"
                        v-if="!isOpen_Menu"
                    />

                    <icon-close
                        class="w-6 h-6" color="#f00"
                        v-if="isOpen_Menu"
                    />

                </button>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- NAVIGATION                                                 -->
    <!-- ========================================================= -->

    <div
        class="container mx-auto max-w-6xl border-t-2 border-red-600"
    >

        <div class="relative flex items-center justify-between">


            <!-- ================================================= -->
            <!-- MOBILE NAVIGATION                                  -->
            <!-- ================================================= -->

            <div
                :class="[
                    isOpen_Menu
                        ? 'translate-x-0 opacity-100'
                        : 'opacity-0 -translate-x-full'
                ]"
                class="
                    absolute
                    left-0
                    right-0
                    top-full
                    w-full

                    px-6
                    py-4

                    bg-primary-sun-0
                    dark:bg-primary-night-0

                    shadow-md

                    transition-all
                    duration-300
                    ease-in-out

                    lg:relative
                    lg:top-0
                    lg:flex
                    lg:w-full
                    lg:translate-x-0
                    lg:items-center
                    lg:bg-transparent
                    lg:p-0
                    lg:opacity-100
                    lg:shadow-none
                    bg-gray-500 bbbg
                    "
                style="z-index:10000000;"
            >

                <div
                    class="
                        flex
                        flex-col
                        items-center
                        w-full
                        space-y-4

                        lg:flex-row
                        lg:space-y-0
                        lg:space-x-3
                        !dark:bg-layout-night-50

                    "
                    style="z-index:10000000;"
                >

                    <!-- HOME -->
                    <LinkHeader_mfx
                        :route-name="route('home.index')"
                        name="Home"
                    />

                    <!-- GRAFFITIS -->
                    <LinkHeader_mfx
                        :route-name="route('home.pna.grafitti')"
                        name="Grafittis"
                    />
                    <LinkHeader_mfx
                        :route-name="route('home.pna.landschaft')"
                        name="Landschaften"
                    />
                    <!-- PORTRAITS -->
                    <LinkHeader_mfx
                        :route-name="route('home.pna.portraits')"
                        name="Portraits"
                    />

                    <!-- KONTAKT -->
                    <LinkHeader_mfx
                        :route-name="route('home.pna.contacts')"
                        name="Kontakt"
                    />


                    <!-- LOGIN -->
                    <template v-if="!$page.props.userdata.user_id">

                        <LinkHeader_mfx
                            :route-name="route('login')"
                            name="Login"
                        />

                    </template>


                    <!-- DARK / LIGHT MODE -->
                    <buttonChangeMode
                        :mode="mode"
                        @changeMode="changeMode"
                    />


                    <!-- ================================================= -->
                    <!-- PROFIL / USER DROPDOWN                            -->
                    <!-- ================================================= -->

                    <div
                        v-if="$page.props.auth.user"
                        class="relative flex w-full justify-center lg:w-auto lg:ml-auto"


                    >


                    <Dropdown align="right">

                            <!-- TRIGGER -->
                            <template #trigger>
      <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-layout-sun-300 dark:focus:border-layout-night-300 transition"
                                        >
                                            <img
                                                class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                                GetProfileImagePath($page.props.auth.user?.profile_photo_url)"
                                                :alt="
                                                    $page.props.userdata
                                                        .full_name
                                                "
                                            />
                                            {{  }}
                                        </button>



                            </template>


                            <!-- DROPDOWN CONTENT -->
                            <template #content>

                                <!-- Anwendung -->
                                <div class="w-full text-center">

                                    <div
                                        class="
                                            block
                                            px-4
                                            py-2
                                            text-xs
                                            text-layout-sun-500
                                            dark:text-layout-night-500
                                        "
                                    >
                                        <span>Startseite</span>
                                    </div>


                                    <DropdownLink
                                        :with-icon="false"
                                        :with-route="true"
                                        :route-name="route('admin.dashboard')"
                                    >
                                        <span class="flex items-center justify-center gap-1 w-full">
                                            <IconDashboard
                                                class="w-4 h-4"
                                                color="#ffa500"
                                            />

                                            <span>
                                                Dashboard
                                            </span>
                                        </span>
                                    </DropdownLink>


                                    <!-- Konto -->
                                    <div
                                        class="
                                            block
                                            px-4
                                            py-2
                                            text-xs
                                            text-layout-sun-500
                                            dark:text-layout-night-500
                                        "
                                    >
                                        Dein Konto
                                    </div>


                                    <!-- Profil -->
                                    <DropdownLink
                                        :with-icon="false"
                                        :with-route="true"
                                        :route-name="route('admin.profile')"
                                    >
                                        <span class="flex items-center justify-center gap-1 w-full">

                                            <IconProfile
                                                class="w-4 h-4"
                                                color="#ffa500"
                                            />

                                            <span>
                                                Profil
                                            </span>

                                        </span>
                                    </DropdownLink>


                                    <!-- MCSL POINTS -->
                                    <DropdownLink
                                        v-if="CheckTRights('mcslpoints')"
                                        :with-icon="false"
                                        :with-route="true"
                                        :route-name="route('admin.mcslpoints')"
                                    >
                                        <span class="flex items-center justify-center gap-1 w-full">

                                            <IconStarThin
                                                class="w-4 h-4"
                                                color="#ffa500"
                                            />

                                            <span>
                                                MCSL Points
                                            </span>

                                        </span>
                                    </DropdownLink>


                                    <!-- PRIVATE NACHRICHTEN -->
                                        <DropdownLink
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="route('pm.index', { tab: 'inbox' })"
                                        >
                                            <span class="flex items-center justify-center gap-1 w-full">

                                                <IconPM
                                                    class="w-4 h-4"
                                                    color="#ffa500"
                                                />

                                                <span>
                                                    Private Nachrichten
                                                </span>

                                            </span>
                                        </DropdownLink>


                                    <!-- KONTAKTE -->
                                    <DropdownLink
                                        :with-icon="false"
                                        :with-route="true"
                                        :route-name="route('admin.contacts')"
                                    >
                                        <span class="flex items-center justify-center gap-1 w-full">

                                            <IconContacts_alt
                                                class="w-4 h-4"
                                                color="#ffa500"
                                            />

                                            <span>
                                                Kontakte
                                            </span>

                                        </span>
                                    </DropdownLink>


                                    <!-- TRENNLINIE -->
                                    <div
                                        class="
                                            my-2
                                            border-t
                                            border-layout-sun-200
                                            dark:border-layout-night-200
                                        "
                                    ></div>


                                    <!-- ABMELDEN -->
                                    <form
                                        @submit.prevent="logoutUser"
                                        class="w-full"
                                    >

                                        <button
                                            type="submit"
                                            class="
                                                flex
                                                items-center
                                                justify-center
                                                gap-1
                                                w-full
                                                px-4
                                                py-2
                                                text-sm
                                                font-medium
                                                font-bold
                                                text-layout-sun-700
                                                hover:text-layout-sun-900
                                                dark:text-layout-night-700
                                                dark:hover:text-layout-night-900
                                                hover:underline
                                                cursor-pointer
                                            "
                                        >

                                            <IconLogout
                                                class="w-4 h-4"
                                                color="#ffa500"
                                            />

                                            <span>
                                                Abmelden</span>

                                        </button>

                                    </form>

                                </div>

                            </template>

                        </Dropdown>

                    </div>

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
            <div class="container mx-auto max-w-6xl mt-[-80px] md:mt-[99px] min-h-screen py-32 px-2">
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
        <footer class="foot bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 border-t border-layout-sun-200 dark:border-layout-night-200" style="z-index:35" aria-labelledby="footer-heading">
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
                        <!-- <li>
                        <link-footer name="Benutzer" :href="route('home.userlist')"></link-footer>
                        </li> -->
                        <li>
                        <link-footer name="Impressum" :route-name="route('home.imprint')"></link-footer>
                        </li>
                        <li>
                        <link-footer name="Datenschutzerklärung" :route-name="route('home.privacy')"></link-footer>
                        </li>
                        <li>
                        <link-footer name="Kontakt" :route-name="route('home.contacts')"></link-footer>
                        </li>
                        <li>
                                        <a
                                            class="ToggleCookieLink cursor-pointer inline-flex items-center gap-2 rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900"
                                            onclick="showHideToggleCookiePreferencesModal()"
                                        >

                                            <IconCookies
                                                width="18"
                                                height="18"
                                                class="mr-[-4px]"
                                                color="#e8c456"
                                            />

                                            <span>
                                                Cookie Einstellungen
                                            </span>

                                        </a>
<!--
                            <LinkFooter @click="reopenCookieBanner">
                            <b>Cookie-Einstellungen</b>
                            </LinkFooter> -->

                        </li>
                    </ul>
                    </div>
                    <div class="text-center md:text-left">
                    <h3 class="text-sm font-semibold leading-6 px-2">
                        <span> Authentifizierung </span>
                    </h3>
                    <ul role="list" class="mt-6 space-y-4  list-none">
                        <li>
                        <link-footer name="Login" :route-name="route('login')"></link-footer>
                        </li>
                        <li>
                        <link-footer name="Registrierung" :route-name="route('register')"></link-footer>
                        </li>
                    </ul>
                    </div>
                </div>
                </div>

                <div class="pt-8 text-layout-sun-700 dark:text-layout-night-700">
                <div class="flex flex-col items-center justify-between text-xs leading-5 gap-4">
                    <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                    <div>
                        <brand-footer :appName="$page.props.applications.app_name"></brand-footer>
                    </div>
                    <div>
                        <!-- <link-footer>
                        <a href="https://www.facebook.com" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                            <icon-facebook class="flex-shrink-0 w-6 h-6"></icon-facebook>
                        </a>
                        </link-footer>
                        <link-footer>
                        <a href="https://www.linkedin.com" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                            <icon-linked-in class="flex-shrink-0 w-6 h-6"></icon-linked-in>
                        </a>
                        </link-footer>
                        <link-footer>
                        <a href="https://youtube" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                            <icon-youtube class="flex-shrink-0 w-6 h-6"></icon-youtube>
                        </a>
                        </link-footer> -->
                    </div>
                    </div>

                    <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                    <div class="text-xs leading-6">
                       &copy; {{ year }}         Eleven/MCSL. Ein Template von Oliver Reinking / Asario.
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
    import axios from "axios";
    // import { useLoadingStore } from '@/loading';
    import IconMCSL from "@/Application/Components/Icons/IconMCSL.vue";
    import IconClose from "@/Application/Components/Icons/Close.vue";
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    // import BrandHeader from "@/Application/Shared/BrandHeader.vue";
    import Dropdown from "@/Application/Components/Content/Dropdown.vue";
    import DropdownLink from "@/Application/Components/Content/DropdownLink.vue";
    import LinkHeader_mfx from "@/Application/Shared/LinkHeader_mfx.vue";
    import IconStarThin from "@/Application/Components/Icons/IconStarThin.vue";
    import IconContacts_alt from "@/Application/Components/Icons/IconContacts_alt.vue";
    import IconLogout from "@/Application/Components/Icons/IconLogout.vue";
    import IconProfile from "@/Application/Components/Icons/IconProfile.vue";
    import { router } from '@inertiajs/vue3'
    import IconPM from "@/Application/Components/Icons/IconPM.vue";
    import IconDashboard from "@/Application/Components/Icons/IconDashboard.vue";
    import BrandFooter from "@/Application/Shared/BrandFooter.vue";
    import Loader from "@/Application/Components/Loader.vue";
    import LinkFooter from "@/Application/Shared/LinkFooter.vue";
       import IconMenu from "@/Application/Components/Icons/Menu.vue"
    import Toast from "@/Application/Components/Content/Toast.vue";
    import buttonChangeMode from "@/Application/Components/ButtonChangeMode.vue";
    import { SD,GetProfileImagePath,CheckTRights } from '@/helpers';
    import IconCookies from "@/Application/Components/Icons/IconCookies.vue";
    import throttle from 'lodash/throttle';
    import pickBy from "lodash/pickBy";
    import {showHideToggleCookiePreferencesModal} from "@/helpers"
    import PnaLogo from "@/Application/Shared/pnalogo.vue";

    export default {
        name: "Homepage_Shared_Layout_pna",

        components: {
        MetaHeader,
        PnaLogo,
        LinkHeader_mfx,
        BrandFooter,
        LinkFooter,
        Toast,
        IconCookies,
        IconLogout,
        Loader,
        IconProfile,
        IconPM,
        IconContacts_alt,
        IconStarThin,
        IconDashboard,
        IconMenu,
        IconMCSL,
        Dropdown,
        DropdownLink,
        buttonChangeMode,
        IconClose,
    },

    props: {
        sd: {
        type: String,
        required: false,
        }
    },

    // setup() {
    //     const loadingStore = useLoadingStore();
    //     return { loadingStore };
    // },

    data() {
        return {
        headerTitle: this.$page?.props?.title ?? "",
        headerDescription: this.$page?.props?.description ?? "",
        headerUrl: this.$page?.props?.url ?? null,
        headerImage: this.$page?.props?.image ?? null,
        mode: (() => {

            if (typeof window === "undefined") {
                return 'dark';
            }

            const savedTheme = localStorage.getItem('theme');

            return savedTheme || 'dark';

        })(),
        isLoading:false,
        isOpen_Menu: false,
        year: new Date().getFullYear(),
        pendingRequests: 0,
        rights: {
            edit: null,
            delete:null,
        },

        // isLoading: localStorage.getItem('loading') === 'true',
        search: '',
        searchval: false,
        imagesLoaded: false,
        searchTimeout: 6000, // Timeout für Inaktivitätsprüfung
        };
    },


    async mounted() {
           this.applyTheme();


        if(typeof window !== "undefined"){
    const params = new URLSearchParams(window.location.search);
    const search = params.get("search");
    // Wenn search gesetzt ist, verstecke das Loading-Div
    if (search && search.trim() !== "") {

        this.setLoading(false);
    }
    else{
        //this.setLoading(true);
    }
        }

    this.rights.delete = await CheckTRights("delete", 'private_messages');

        // const shouldReload = localStorage.getItem('reload_dashboard');

        // if (shouldReload) {
        // localStorage.removeItem('reload_dashboard');
        // }

        // Den 'search' Parameter prüfen
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');

        this.search = searchParam ?? '';

        if (searchParam === '' || searchParam === null) {
        this.setLoadingState(true);
        this.searchval = true;

        // Startet Timeout für spätere leere Suche
        this.startSearchTimeout();
        } else {
        this.setLoadingState(false);
        this.searchval = false;
        }

        // Axios Interceptoren
        axios.interceptors.request.use((config) => {
        this.pendingRequests += 1;
        this.setLoadingState(this.searchval);
        return config;
        });

        axios.interceptors.response.use(
        (response) => {
            this.pendingRequests -= 1;
            this.checkLoadingState();
            return response;
        },
        (error) => {
            this.pendingRequests -= 1;
            this.checkLoadingState();
            return Promise.reject(error);
        }
        );

        // Bilder laden überwachen
        this.waitForImagesToLoad();

        if (this.isLoading) {
        if(typeof window !== "undefined")
            {
            localStorage.setItem('loading', 'true');
            }
        }
    },
    watch: {
  form: {
    handler: throttle(function () {
      const query = pickBy(this.form); // Entfernt leere Felder

      router.get(
        this.route(
          "home.index",
          Object.keys(query).length ? query : { search: null, table: this.table } // leere Suche zurücksetzen
        ),
        this.form,
        {
          preserveState: true,
          replace: true,
        }
      );
    }, 500),
    deep: true,
  },
//  '$page.url'() {
//         this.applyTheme();
//     },
},

    methods: {
        GetProfileImagePath,
        SD,
        showHideToggleCookiePreferencesModal,
        CheckTRights,
            mupper(text) {
               return text;
            },

   applyTheme() {

        const html =
            document.documentElement;





        /*
        |--------------------------------------------------------------------------
        | Force Light
        |--------------------------------------------------------------------------
        */

        const forceLight =
            window.location.pathname === '/login'
            || window.location.pathname === '/register';

        if (forceLight) {


            html.classList.remove('dark');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Theme anwenden
        |--------------------------------------------------------------------------
        */

        if (this.mode === 'dark') {


            html.classList.add('dark');

        } else {



            html.classList.remove('dark');
        }


    },

   changeMode(newMode) {

    this.mode = newMode;
    if(typeof newMode === "undefined")
    {
        newMode = 'dark';
    }
    const forceLight =
        window.location.pathname === '/login'
        || window.location.pathname === '/register';

    if (!forceLight) {
        localStorage.setItem('theme', newMode);
    }

    this.applyTheme();
},




        setLoadingState(state) {
        this.isLoading = state;
        if(typeof window !== "undefined")
        {
            localStorage.setItem('loading', state ? state.toString():'');
        }
    },

        checkLoadingState() {
        if (this.pendingRequests === 0 && this.imagesLoaded) {
            this.setLoadingState(false);
        }
        },

        waitForImagesToLoad() {
        const images = document.querySelectorAll('img');
        const totalImages = images.length;
        let imagesLoadedCount = 0;

        if (totalImages === 0) {
            this.imagesLoaded = true;
            this.checkLoadingState();
            return;
        }

        images.forEach((img) => {
            if (img.complete) {
            imagesLoadedCount++;
            } else {
            img.addEventListener('load', () => {
                imagesLoadedCount++;
                if (imagesLoadedCount === totalImages) {
                this.imagesLoaded = true;
                this.checkLoadingState();
                }
            });
            }
        });

        if (imagesLoadedCount === totalImages) {
            this.imagesLoaded = true;
            this.checkLoadingState();
        }

        },
        // checkLoadingState() {
        // if (this.pendingRequests === 0 && this.imagesLoaded) {
        //     this.setLoadingState(false);
        // }
        // },

        // waitForImagesToLoad() {
        // const images = document.querySelectorAll('img');
        // const totalImages = images.length;
        // let imagesLoadedCount = 0;

        // if (totalImages === 0) {
        //     this.imagesLoaded = true;
        //     this.checkLoadingState();
        //     return;
        // }

        // images.forEach((img) => {
        //     if (img.complete) {
        //     imagesLoadedCount++;
        //     } else {
        //     img.addEventListener('load', () => {
        //         imagesLoadedCount++;
        //         if (imagesLoadedCount === totalImages) {
        //         this.imagesLoaded = true;
        //         this.checkLoadingState();
        //         }
        //     });
        //     }
        // });

        // if (imagesLoadedCount === totalImages) {
        //     this.imagesLoaded = true;
        //     this.checkLoadingState();
        // }
        // },

        toggleNavbar() {
        this.isOpen_Menu = !this.isOpen_Menu;
        },

        // changeMode() {
        // this.mode = this.mode === "dark" ? "light" : "dark";
        // if(typeof window !== "undefined")
        // {
        // localStorage.theme = this.mode;
        // }
        // },

        logoutUser() {
            console.log("Logout geklickt");
            router.post('/logout');
        },

        // Startet 3-Sekunden-Timeout, wenn der Nutzer mit Tippen aufhört
        startSearchTimeout() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            if (this.search.trim() !== '') {
            this.setLoadingState(true);
            }
        },6000);
        },

        // Bei Eingabe im Suchfeld
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
    .dark .bbb{background-color:#363737 !important;}
    .foot {
    position: relative;
    z-index: 35;
    }
    .dark .bbbg{
        background-color: #353636 !important;
    }
    .dark .bbb:hover{
        background-color: #555 !important;
        color:#ed4d4d !important;
    }
    .dark .bbg{
        background-color: #000  !important;
    }
    .dark .tbb{
        color:#FFF !important;
    }
    /* Deine Styles hier */
    </style>




