import './bootstrap';
import '../css/app.css';
import 'material-icons/iconfont/material-icons.css';
import 'material-symbols';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from 'primevue/config';
import Lara from '@/presets/lara';
import 'primeicons/primeicons.css';
import Ripple from 'primevue/ripple';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import FocusTrap from 'primevue/focustrap';
import ConfirmationService from 'primevue/confirmationservice';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                unstyled: true,
                pt: Lara,
                ripple: true,
            })
            .use(ToastService)
            .use(ConfirmationService)
            .directive('ripple', Ripple)
            .directive('tooltip', Tooltip)
            .directive('focustrap', FocusTrap)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
