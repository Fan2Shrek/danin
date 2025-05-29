<script setup lang="ts">
import { ref, inject, watch } from 'vue';
import tokens from '@/i18n/tokens';

import type { LoginResponse } from '@/lib/api/resources/user';

const email = ref('');
const password = ref('');
const errorMessage = ref('');

const animate = ref(false);

const totpCode = ref('');
const step = ref(1);

const loginUser = inject<(username: string, password: string) => LoginResponse>('loginUser');
const verifyCode = inject<(code: string) => null>('verifyCode');

if (!loginUser || !verifyCode) {
    throw new Error('loginUser function is not provided');
}

let timerId: ReturnType<typeof setTimeout> | null = null;

watch(animate, (newVal) => {
    if (newVal) {
        if (timerId) clearTimeout(timerId);
        timerId = setTimeout(() => {
            animate.value = false;
        }, 4000);
    }
});

const handleLogin = async () => {
    if (!email.value || !password.value) {
        errorMessage.value = tokens.login.error.empty;
        return;
    }

    try {
        errorMessage.value = '';
        const response = await loginUser(email.value, password.value);

        if (response.need_totp) {
            animate.value = true;
            step.value = 2;
            return;
        }
    } catch (error) {
        console.error(error);
        errorMessage.value = tokens.login.error.invalid;
    }
};

const handleCode = async () => {
    if (!totpCode.value) {
        errorMessage.value = tokens.login.error.empty;
        return;
    }

    try {
        errorMessage.value = '';
        await verifyCode(totpCode.value);
    } catch {
        errorMessage.value = tokens.login.totp.error;
    }
};
</script>

<template>
    <div class="login-container" :class="{ animate: animate }">
        <div class="login-box">
            <Transition name="slide-left">
                <div v-if="step === 1">
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
                        <button type="submit" class="submit-button">
                            {{ $t(tokens.login.submit) }}
                        </button>

                        <p class="register-link">
                            {{ $t(tokens.login.register.link) }}
                            <!-- todo link to register page -->
                            <a href="#">{{ $t(tokens.login.register.cta) }}</a>
                        </p>
                    </form>
                </div>
            </Transition>
            <Transition name="slide-right">
                <div v-if="step === 2">
                    <h1>{{ $t(tokens.login.totp.title) }}</h1>
                    <form @submit.prevent="handleCode">
                        <div class="form-group">
                            <label>{{ $t(tokens.login.totp.input) }}</label>
                            <input type="text" v-model="totpCode" class="form-input" />
                        </div>
                        <button type="submit" class="submit-button">
                            {{ $t(tokens.login.totp.submit) }}
                        </button>
                    </form>
                    <p class="error-message">{{ $t(errorMessage) }}</p>
                </div>
            </Transition>
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
        overflow: hidden;
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

    .totp-container {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
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
        width: 100%;
        margin-top: 1rem;

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

.slide-left-enter-active,
.slide-left-leave-active {
    transition: all 0.2s ease;
}
.slide-left-enter-from {
    transform: translateX(100%);
    opacity: 0;
}
.slide-left-leave-to {
    transform: translateX(-100%);
    opacity: 0;
}

.slide-right-enter-active,
.slide-right-leave-active {
    transition: all 10s ease;
}
.slide-right-enter-from {
    transform: translateX(100%);
    opacity: 0;
}
.slide-right-leave-to {
    transform: translateX(-100%);
    opacity: 0;
}
@keyframes rotatelascar {
    to {
        rotate: 2turn;
    }
}

@keyframes scalelascar {
    0% {
        transform: scaleY(1) scaleX(1);
    }
    25% {
        transform: scaleX(0.1) scaleY(1);
    }
    50% {
        transform: scaleX(1) scaleY(1);
    }
    75% {
        transform: scaleX(1) scaleY(0.1);
    }
    100% {
        transform: scaleY(1) scaleX(1);
    }
}

@keyframes filterlascar {
    50% {
        filter: blur(7px);
    }
    100% {
        filter: blur(0);
    }
}

.animate {
    &,
    > * {
        animation:
            scalelascar 3s infinite linear,
            rotatelascar 3s infinite linear,
            filterlascar 1s infinite linear;
    }
}
</style>
