import { createRouter, createWebHistory } from 'vue-router';

import LoginPage from '../page/LoginPage.vue';
import TchatPage from '../page/room/TchatPage.vue';
import { getCookie } from '../lib/cookies';

function isAuthenticated() {
    const cookie = getCookie('token');

    return cookie !== null;
}

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/login',
            name: 'Login',
            component: LoginPage,
        },
        {
            path: '/room/tchat',
            name: 'Tchat',
            component: TchatPage,
            meta: { requiresAuth: true },
        },
    ],
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        next({ name: 'Login' });
    } else {
        next();
    }
});

export default router;
