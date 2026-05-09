import { toastBus } from '@/utils/toastBus';

export default {
    install(app) {
        if (typeof window !== 'undefined') {
            window.toast = (type, message, duration = 15000) =>
                toastBus.emit({ type, message, duration });
        }

        app.config.globalProperties.$toast = toastBus;
    }
};
