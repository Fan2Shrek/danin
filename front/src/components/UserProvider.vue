<script setup lang="ts">
import { provide } from 'vue';
import { useRouter } from 'vue-router';

import api from '@/lib/api/api';
import { setCookie, deleteCookie } from '@/lib/cookies';

import type { Response } from '@/lib/api/resources/user';

const router = useRouter();

const onLoginSuccess = (token: string, refreshToken: string | null = null) => {
    api().setToken(token);
    const decoded = atob(token.split('.')[1]);
    setCookie('token', token, new Date(JSON.parse(decoded).exp * 1000));

    if (refreshToken) {
        const date = new Date();
        date.setDate(date.getDate() + 30);
        setCookie('refresh_token', refreshToken, date);
    }

    router.push({ name: 'Home' });
};

const loginUser = async (username: string, password: string): Promise<Response> => {
    const response = await api().user().login(username, password);

    if (response.need_totp) {
        api().setToken(response.token); // need to set temp token

        return response;
    }

    onLoginSuccess(response.token, response.refresh_token);

    return response;
};

const verifyCode = async (code: string) => {
    const response = await api().user().verifyCode(code);

    onLoginSuccess(response.token);
};

const logoutUser = () => {
    deleteCookie('token');
};

const user = {};

provide('loginUser', loginUser);
provide('verifyCode', verifyCode);
provide('logoutUser', logoutUser);
provide('user', user);
</script>

<template>
    <slot />
</template>
