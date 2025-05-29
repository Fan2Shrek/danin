<script setup lang="ts">
import { ref, provide } from 'vue';
import { useRouter } from 'vue-router';

import api from '@/lib/api/api';
import { getCookie, setCookie, deleteCookie } from '@/lib/cookies';

import type { Response, User } from '@/lib/api/resources/user';

const router = useRouter();

const user = ref<User | null>(null);

if (getCookie('token')) {
    api().setToken(getCookie('token')!);
    api()
        .user()
        .me()
        .then((res) => {
            user.value = res;
        })
        .catch(() => {
            logoutUser();
        });
}

const onLoginSuccess = async (token: string, refreshToken: string | null = null) => {
    api().setToken(token);
    const decoded = atob(token.split('.')[1]);
    setCookie('token', token, new Date(JSON.parse(decoded).exp * 1000));

    if (refreshToken) {
        const date = new Date();
        date.setDate(date.getDate() + 30);
        setCookie('refresh_token', refreshToken, date);
    }
    user.value = await api().user().me();

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
    user.value = null;
    deleteCookie('token');
    deleteCookie('refresh_token');
};

provide('loginUser', loginUser);
provide('verifyCode', verifyCode);
provide('logoutUser', logoutUser);
provide('user', user);
</script>

<template>
    <slot />
</template>
