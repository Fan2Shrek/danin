import { createRouter, createWebHistory } from 'vue-router';

import LoginPage from '../page/LoginPage.vue';
import HomePage from '../page/HomePage.vue';
import RegisterPage from '../page/RegisterPage.vue';
import TchatPage from '../page/room/TchatPage.vue';
import CreateRoomPage from '../page/room/CreatePage.vue';
import StartRoom from '../page/room/StartPage.vue';
import ArticlePage from '../page/ArticlePage.vue';
import GamesPage from '@/page/GamesPage.vue';

import { getCookie } from '../lib/cookies';
import ProfilePage from '@/page/ProfilePage.vue';

function isAuthenticated() {
    const cookie = getCookie('token') || getCookie('refresh_token');

    return cookie !== null;
}

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: HomePage,
        },
        {
            path: '/login',
            name: 'Login',
            component: LoginPage,
        },
        {
            path: '/register',
            name: 'Register',
            component: RegisterPage,
        },
        {
            path: '/games',
            name: 'Games',
            component: GamesPage,
        },
        {
            path: '/room/create',
            name: 'CreateRoom',
            component: CreateRoomPage,
            meta: { requiresAuth: true },
        },
        {
            path: '/room/:id/start',
            name: 'StartRoom',
            component: StartRoom,
            meta: { requiresAuth: true },
        },
        {
            path: '/room/:id/tchat',
            name: 'Tchat',
            component: TchatPage,
            meta: { requiresAuth: true },
        },
        {
            path: '/article/:slug',
            name: 'Article',
            component: ArticlePage,
        },
        {
            path: '/profile',
            name: 'Profile',
            component: ProfilePage,
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
