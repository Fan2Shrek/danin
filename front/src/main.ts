import './assets/main.css';

import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import { createPinia } from 'pinia';
import { createHead } from '@vueuse/head';
import mitt from 'mitt';
// @ts-expect-error et tout
import VueMatomo from 'vue-matomo';

import App from './App.vue';
import router from './router';
import en from '@/i18n/en.ts';
import fr from '@/i18n/fr.ts';

const app = createApp(App);

const i18n = createI18n({
    locale: 'fr',
    fallbackLocale: 'en',
    messages: {
        fr: fr,
        en: en,
    },
});

const emitter = mitt();
const pinia = createPinia();

app.config.globalProperties.emitter = emitter;

app.use(router);
app.use(i18n);
app.use(pinia);
app.use(createHead());
app.use(VueMatomo, {
    host: import.meta.env.VITE_MATOMO_URL,
    siteId: 1,
});

app.mount('#app');

// @ts-expect-error et tout
window._paq.push(['trackPageView']);
