import { createSSRApp, h } from 'vue'
import { renderToString } from '@vue/server-renderer'

import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'

import { route as ziggyRoute, ZiggyVue } from 'ziggy-js'
import { createPinia } from 'pinia'

import Toast from './Application/Components/Content/Toast.vue'
import { Ziggy } from './ziggy'

// =====================================
// GLOBAL SSR SAFE ZIGGY
// =====================================

globalThis.Ziggy = Ziggy

globalThis.route = (
    name = null,
    params = {},
    absolute = true
) => {

    if (!name) {
        return {
            current: () => false,
            has: () => false,
            params: {},
        }
    }

    try {
        return ziggyRoute(name, params, absolute, globalThis.Ziggy)
    } catch (e) {

        console.error('SSR route error:', name)

        return {
            current: () => false,
            has: () => false,
            params: {},
            toString: () => '#',
        }
    }
}

// =====================================
// PAGES
// =====================================

const pages = import.meta.glob('./Application/**/*.vue', {
    eager: true,
})

// =====================================
// SSR SERVER
// =====================================

createServer((page) => {
    return createInertiaApp({
        page,

        render: renderToString,

        resolve: (name) => {

            const resolvedPage =
                pages[`./Application/${name}.vue`]

            if (!resolvedPage) {
                throw new Error(
                    `SSR Page not found: ./Application/${name}.vue`
                )
            }

            return resolvedPage
        },

        setup({ App, props, plugin }) {

            // =====================================
            // SSR SAFE ZIGGY CONFIG
            // =====================================

            const ziggy = {
                ...Ziggy,
                ...(props.initialPage?.props?.ziggy ?? {}),
                location: new URL(
                    props.initialPage?.props?.ziggy?.location ||
                    Ziggy.url ||
                    'http://localhost'
                ),
            }

            globalThis.Ziggy = ziggy

            globalThis.route = (
                name,
                params = {},
                absolute = true
            ) => {

                try {
                    return ziggyRoute(
                        name,
                        params,
                        absolute,
                        ziggy
                    )
                } catch (e) {

                    console.error(
                        'SSR route error:',
                        name,
                        e.message
                    )

                    return '#'
                }
            }

            // =====================================
            // APP
            // =====================================

            const app = createSSRApp({
                render: () => h(App, props),
            })

            // =====================================
            // PINIA
            // =====================================

            const pinia = createPinia()

            // =====================================
            // PLUGINS
            // =====================================

            app.use(plugin)
            app.use(pinia)
            app.use(ZiggyVue, ziggy)

            // =====================================
            // GLOBAL COMPONENTS
            // =====================================

            app.component('Head', Head)
            app.component('Link', Link)
            app.component('Toast', Toast)

            // =====================================
            // GLOBAL PROPERTIES
            // =====================================

            app.config.globalProperties.route =
                globalThis.route

            app.config.globalProperties.$route =
                globalThis.route

            // =====================================
            // SSR SAFE DIRECTIVES
            // =====================================

            app.directive('tippy', {
                mounted() {},
                updated() {},
            })

            return app
        },
    })
})
