<script setup lang="ts">
import { provide } from 'vue';
import { useRouter } from 'vue-router';

import api from '@/lib/api/api';
import { setCookie, deleteCookie } from '@/lib/cookies';

const router = useRouter();

const loginUser = async (username: string, password: string) => {
    const response = await api().user().login(username, password);
    api().setToken(response.token);
    const decoded = atob(response.token.split('.')[1]);
    setCookie('token', response.token, new Date(JSON.parse(decoded).exp * 1000));

    if (response.refresh_token) {
        const date = new Date();
        date.setDate(date.getDate() + 30);
        setCookie('refresh_token', response.refresh_token, date);
    }

    router.push({ name: 'Home' });
};

const logoutUser = () => {
    deleteCookie('token');
};

const user = {};

provide('loginUser', loginUser);
provide('logoutUser', logoutUser);
provide('user', user);
</script>

<template>
    <slot />
</template>
