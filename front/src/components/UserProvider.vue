<script setup lang="ts">
import { provide } from 'vue';
import { useRouter } from 'vue-router';

import api from '@/lib/api/api';
import { setCookie, deleteCookie } from '@/lib/cookies';

const router = useRouter();

const loginUser = async (username: string, password: string) => {
    const response = await api().user().login(username, password);
    api().setToken(response.token);
    setCookie('token', response.token);
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
