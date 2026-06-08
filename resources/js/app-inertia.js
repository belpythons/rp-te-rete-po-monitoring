import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import './bootstrap';

/**
 * -----------------------------------------------
 * Inertia App Setup
 * -----------------------------------------------
 */
createInertiaApp({
    title: (title) => title ? `${title} — Procurement System` : 'Procurement System',

    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Make Echo available globally in Vue components
        app.config.globalProperties.$echo = window.Echo;

        app.use(plugin);
        app.mount(el);
    },
});
