<script setup lang="ts">
import { ref } from 'vue';
import tokens from '@/i18n/tokens';

import api from '@/lib/api/api';
import { setCookie } from '@/lib/cookies';

const email = ref('');
const password = ref('');
const errorMessage = ref('');

const handleLogin = async () => {
    if (!email.value || !password.value) {
        errorMessage.value = tokens.login.error.empty;
        return;
    }

    try {
        // todo set the cookie
        const response = await api().user().login(email.value, password.value);
        api().setToken(response.token);
        setCookie('token', response.token);
    } catch (error) {
        console.error(error);
        errorMessage.value = tokens.login.error.invalid;
    }
};
</script>

<template>
    <div class="login-container">
        <div class="login-box">
            <h1>{{ $t(tokens.login.title) }}</h1>
            <form @submit.prevent="handleLogin" class="login-form">
                <div class="form-group">
                    <label>{{ $t(tokens.login.email) }}</label>
                    <input type="text" v-model="email" class="form-input" />
                </div>
                <div class="form-group">
                    <label>{{ $t(tokens.login.password) }}</label>
                    <input type="password" v-model="password" class="form-input" />
                </div>
                <p v-if="errorMessage" class="error-message">{{ $t(errorMessage) }}</p>
                <button type="submit" class="submit-button">{{ $t(tokens.login.submit) }}</button>

                <p class="register-link">
                    {{ $t(tokens.login.register.link) }}
                    <!-- todo link to register page -->
                    <a href="#">{{ $t(tokens.login.register.cta) }}</a>
                </p>
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">
.login {
    &-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
    }

    &-box {
        background: #ffffff;
        padding: 2.5rem;
        width: 100%;
        max-width: 400px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    &-form {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    &-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
}

.form {
    &-input {
        width: 100%;
        padding: 0.75rem;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #333;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 1rem;
        box-sizing: border-box;
        transition: border-color 0.2s ease;

        &:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }
    }
}

.submit {
    &-button {
        background: #4a90e2;
        color: #fff;
        padding: 0.875rem;
        border: none;
        border-radius: 4px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s ease;

        &:hover {
            background: #357abd;
        }

        &:active {
            background: #2d6da3;
        }
    }
}

.register {
    &-link {
        text-align: center;
        color: #666;
        margin-top: 1rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 0.875rem;

        a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 500;

            &:hover {
                text-decoration: underline;
            }
        }
    }
}

h1 {
    color: #333;
    text-align: center;
    font-size: 1.75rem;
    margin-bottom: 2rem;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-weight: 500;
}

label {
    color: #555;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
}

.error-message {
    color: #dc3545;
    text-align: center;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 0.875rem;
}
</style>
