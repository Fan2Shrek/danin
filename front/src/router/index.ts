import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../page/LoginPage.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: LoginPage,
        },
    ],
})

export default router
