import './assets/main.css'

import { createApp } from 'vue'
import { createI18n } from 'vue-i18n'

import App from './App.vue'
import router from './router'
import en from '@/i18n/en.ts'
import fr from '@/i18n/fr.ts'

const app = createApp(App)

const i18n = createI18n({
    locale: 'fr',
    fallbackLocale: 'en',
    messages: {
        fr: fr,
        en: en,
    },
})

app.use(router)
app.use(i18n)
app.mount('#app')
