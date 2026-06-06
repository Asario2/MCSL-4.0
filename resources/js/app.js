console.log('APP JS LOADED');
import './ziggy-global';
import './bootstrap/client';

import '../css/app.css';
import '@fontsource/open-sans/index.css';
import '@fontsource/ubuntu/index.css';

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { i18nVue } from "laravel-vue-i18n";
import { route } from 'ziggy-js';
import { Ziggy } from './ziggy';
import { router } from '@inertiajs/vue3'
import { createPinia } from "pinia";
import axios from "axios";

// ======================
// TIPPY (FIXED)
// ======================
import { plugin as VueTippy } from 'vue-tippy';
import 'tippy.js/dist/tippy.css';

// FontAwesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPencilAlt, faTrashCan } from "@fortawesome/free-solid-svg-icons";
import { faXTwitter } from '@fortawesome/free-brands-svg-icons';
library.add(faPencilAlt, faTrashCan, faXTwitter);

// Toast
import { toastBus } from './utils/toastBus';
import Toast from './Application/Components/Content/Toast.vue';

// ClientOnly
import ClientOnly from "@/Application/Components/ClientOnly.vue";

// ======================
// SSR SAFE FIXES
// ======================
if (typeof window !== 'undefined' && window.trustedTypes) {
    window.trustedTypes.createPolicy('default', {
        createScript: (input) => input,
        createScriptURL: (input) => input
    });
}

if (typeof global === 'undefined') {
    window.global = window;
}

// ======================
// SAFE ROUTE
// ======================
function safeRoute(name, params = {}) {
    try {
        return route(name, params, false, Ziggy);
    } catch (error) {
        if (error.message.includes('is not in the route list')) {
            console.error(`Ziggy: Route '${name}' nicht gefunden.`);
            router.visit('/404');
            return '/404';
        }
        throw error;
    }
}

// ======================
// AXIOS
// ======================
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// ======================
// AXIOS INTERCEPTORS (GLOBAL)
// ======================
if (typeof window !== 'undefined') {

    let pendingRequests = 0;

    axios.interceptors.request.use((config) => {
        pendingRequests++;

        window.dispatchEvent(new CustomEvent("axios:start", {
            detail: { pendingRequests }
        }));

        return config;
    });

    axios.interceptors.response.use(
        (response) => {
            pendingRequests--;

            window.dispatchEvent(new CustomEvent("axios:finish", {
                detail: { pendingRequests }
            }));

            return response;
        },
        (error) => {
            pendingRequests--;

            window.dispatchEvent(new CustomEvent("axios:finish", {
                detail: { pendingRequests }
            }));

            return Promise.reject(error);
        }
    );
}


if (typeof document !== 'undefined') {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (token) {
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
    }
}

// ======================
// INERTIA EVENTS
// ======================
router.on("start", (event) => {
    if (event.detail?.visit?.skipLoading) return;
    window.dispatchEvent(new CustomEvent("loader:show"));
});

router.on("finish", (event) => {
    if (event.detail?.visit?.skipLoading) return;
    window.dispatchEvent(new CustomEvent("loader:hide"));
});

import { SD } from "@/helpers";
// ======================
// APP
// ======================

createInertiaApp({
    title: title => `${title} - ${SD(1)}`,

    resolve: async (name) => {

    console.log('Loading page:', name);

    const page = await resolvePageComponent(
        `./Application/${name}.vue`,
        import.meta.glob("./Application/**/*.vue")
    );

    console.log('Page loaded:', page);

    return page;
},

    setup({ el, App, props, plugin }) {

        if (!el) {
            console.error("Inertia mount Element fehlt!");
            return;
        }

        const app = createApp({
            render: () => h(App, props),
        });

        // Plugins
        app.use(createPinia());
        app.use(plugin);

        // ======================
        // TIPPY FIX (WICHTIG)
        // ======================
        app.use(VueTippy, {
            directive: 'tippy',
            component: 'tippy',
            defaultProps: {
                placement: 'top',
                allowHTML: true,
            }
        });

        // i18n
        app.use(i18nVue, {
            resolve: async lang => {
                const langs = import.meta.glob("../../lang/*.json");
                if (!langs[`../../lang/${lang}.json`]) return {};
                return await langs[`../../lang/${lang}.json`]();
            },
        });

        // Components
        app.component("font-awesome-icon", FontAwesomeIcon);
        app.component("Toast", Toast);
        app.component("ClientOnly", ClientOnly);

        // Globals
        app.config.globalProperties.$route = safeRoute;
        app.config.globalProperties.route = safeRoute;
        app.config.globalProperties.$toast = toastBus;

        if (typeof window !== 'undefined') {
            window.route = safeRoute;
            window.toastBus = toastBus;

            window.toast = (type, message, duration = 5000) =>
                toastBus.emit({ type, message, duration });
        }

        app.mount(el);

        // ======================
        // REST DEINER LOGIK (UNVERÄNDERT)
        // ======================
        if (props.flash?.needsReload && !sessionStorage.getItem('needsReload')) {
            sessionStorage.setItem('needsReload', '1');
            window.location.reload();
        } else if (sessionStorage.getItem('needsReload')) {
            sessionStorage.removeItem('needsReload');
        }

        async function loadDarkMode() {
            let mode = localStorage.getItem("theme") || "light";

            try {
                const response = await fetch("/api/dark-mode");
                const data = await response.json();
                mode = data.darkMode || mode;
            } catch (e) {}

            localStorage.setItem("theme", mode);
            document.documentElement.setAttribute("data-theme", mode);
        }

        if (typeof window !== 'undefined') {
            loadDarkMode();
        }

        if (typeof window !== 'undefined') {
            window.$_GET = {};
            const query = window.location.search.substring(1);

            if (query) {
                query.split('&').forEach(param => {
                    const [key, val] = param.split('=').map(decodeURIComponent);
                    window.$_GET[key] = val;
                });
            }
        }

        if (typeof window !== 'undefined') {
            let oldUrl = window.location.href;

            window.addEventListener('beforeunload', function () {
                const newUrl = document.activeElement?.href || '';
                if (newUrl && newUrl !== oldUrl) {
                    navigator.sendBeacon('/unset-session.php');
                }
            });
        }
    },

    progress: {
        color: "#0EA5E9",
    },
});
