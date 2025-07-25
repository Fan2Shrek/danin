<script setup lang="ts">
import { ref, defineProps, inject } from 'vue';

import type { User } from '@/lib/api/resources/user';

const toggle = ref(false);

const logout = inject<() => void>('logoutUser');

defineProps<{
    user: User | null;
}>();
</script>

<template>
    <div v-if="user" class="user__badge">
        <div class="user__badge--image" @click="toggle = !toggle">
            <p>{{ user.username.charAt(0).toUpperCase() }}</p>
        </div>
        <div v-if="toggle" class="user__badge--info">
            <div class="user__badge--info__header">
                <span class="username">{{ user.username }}</span>
                <span>{{ user.email }}</span>
            </div>
            <div class="user__badge--info__body">
                <!-- todo profile page -->
                <router-link to="/profile" class="link">
                    {{ $t('navbar.links.profile') }}
                </router-link>
                <a @click="logout" class="link">
                    {{ $t('navbar.links.logout') }}
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.user__badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #333;
    cursor: pointer;
    position: absolute;
    top: 1.25rem;
    right: 5.5rem;
    height: 1.5rem;

    &--image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background-color: #e0e0e0;
        border-radius: 50%;
    }

    &--info {
        position: absolute;
        top: 100%;
        right: 0;
        transform: translateX(40%);
        margin-top: 0.5rem;
        min-width: 150px;
        max-width: 200px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0.5rem;

        &__header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem;
            border-bottom: 1px solid #ddd;

            .username {
                text-transform: capitalize;
            }
        }

        &__body {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem;
            margin-top: 0.5rem;

            .link {
                text-decoration: none;
                color: #007bff;
                transition: color 0.2s;

                &:hover {
                    color: #0056b3;
                }
            }
        }
    }
}
</style>
