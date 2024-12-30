import '../css/app.css';
import './bootstrap';
import AOS from 'aos';
import 'aos/dist/aos.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(VueSweetalert2);

        // Initialize AOS after the app mounts
        app.mount(el);
        AOS.init();

        // Refresh AOS on every Inertia page navigation
        delete el.dataset.page;
        app.mixin({
            mounted() {
                AOS.refresh();
            },
        });

        window.Swal = app.config.globalProperties.$swal;

        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
