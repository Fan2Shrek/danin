<script setup lang="ts">
import { ref, inject } from 'vue';

import tokens from '@/i18n/tokens';
import UserBadge from '@/components/ui/UserBadge.vue';
import LanguageSwitcher from '@/components/i18n/LanguageSwitcher.vue';

import type { User } from '@/lib/api/resources/user';

const links = {
    [tokens.navbar.links.games]: { name: 'Games' },
    [tokens.navbar.links.tchat]: '/',
    [tokens.navbar.links.createRoom]: { name: 'CreateRoom' },
};

const user = inject<User | null>('user');

const isOpen = ref(false);
const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};
</script>

<template>
    <nav class="navbar">
        <div class="navbar__brand">
            <button
                class="navbar__toggle"
                @click="toggleMenu"
                aria-label="Toggle navigation"
                :aria-expanded="isOpen ? 'true' : 'false'"
            >
                <span class="navbar__toggle-line"></span>
                <span class="navbar__toggle-line"></span>
                <span class="navbar__toggle-line"></span>
            </button>
            <RouterLink :to="{ name: 'Home' }" title="Accueil" class="navbar__title">
                <img src="/D.png" alt="Logo" width="40px" height="40px" />
                <span>Danin</span>
            </RouterLink>
        </div>

        <ul class="navbar__links" :class="{ 'navbar__links--expanded': isOpen }">
            <li v-for="(link, name) in links" :key="name">
                <RouterLink :to="link">{{ $t(String(name)) }}</RouterLink>
            </li>
            <li v-if="!user" class="last-link">
                <RouterLink :to="{ name: 'Login' }">{{ $t(tokens.navbar.links.login) }}</RouterLink>
            </li>
        </ul>

        <UserBadge :user="user" v-if="user" />
        <LanguageSwitcher />
    </nav>
</template>

<style scoped lang="scss">
.navbar {
    background: white;
    display: flex;
    flex-direction: column;
    position: fixed;
    padding: 0.75rem 2rem;
    width: 100%;
    z-index: 20;
    box-shadow:
        0 1px 3px 0 rgb(0 0 0 / 10%),
        0 1px 2px -1px rgb(0 0 0 / 10%);

    &__brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    &__title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #101828;
        text-decoration: none;
        font-size: 2rem;
        line-height: 1.25;

        & img {
            width: 2.5rem;
            height: 2.5rem;
        }
    }

    &__toggle {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        background-color: transparent;
        border: none;
        cursor: pointer;
        padding: 0.5rem;

        &-line {
            width: 1.25rem;
            height: 0.125rem;
            background-color: #101828;
            border-radius: 0.25rem;
        }
    }

    &__links {
        overflow: hidden;
        list-style: none;
        padding: 0;
        max-height: 0;
        transition: all 0.5s ease-in-out;

        & li a {
            position: relative;
            display: inline-block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1.125rem;
            color: #6a7282;
            text-decoration: none;
            border-radius: 0.375rem;

            &:hover {
                color: #3b82f6;
                background-color: rgb(59, 130, 246, 0.1);
            }
        }

        &--expanded {
            margin-top: 0.375rem;
            max-height: 15rem;
        }
    }
}

@media (min-width: 768px) {
    .navbar {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding-right: 9rem;

        &__links {
            display: flex;
            gap: 1rem;
            max-height: none;
            margin-top: 0;
        }

        &__toggle {
            display: none;
        }

        &__links li a {
            padding: 0;

            &:hover {
                color: #101828;
                background-color: transparent;
            }

            &::after {
                content: '';
                position: absolute;
                width: 100%;
                transform: scaleX(0);
                height: 2px;
                bottom: 0;
                left: 0;
                background-color: #101828;
                transform-origin: bottom;
                transition: transform 0.2s ease-out;
            }

            &:hover::after {
                transform: scaleX(1);
            }
        }

        .last-link {
            margin-right: 10px;
        }
    }
}
</style>
