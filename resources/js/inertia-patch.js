import { router } from '@inertiajs/vue3'

// Originalfunktion patchen
const originalVisit = router.visit

router.visit = function (url, options = {}) {
    // Null-Werte aus Daten entfernen
    if (options.data) {
        options.data = Object.fromEntries(
            Object.entries(options.data).filter(([_, value]) => value !== null)
        )
    }

    return originalVisit(url, options)
}

export default Inertia
