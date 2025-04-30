import { createRouter, createWebHistory } from 'vue-router';

import LoginPage from '../page/LoginPage.vue';
import TchatPage from '../page/room/TchatPage.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: LoginPage,
        },
        {
            path: '/room/tchat',
            name: 'tchat',
            component: TchatPage,
        },
    ],
});

export default router;
