<script setup lang="ts">
import { inject } from 'vue';

import type { User } from '@/lib/api/resources/user';
import UserForm from '@/components/form/UserForm.vue';
import EnableTotpForm from '@/components/form/EnableTotpForm.vue';

const user = inject<User | null>('user');
</script>

<template>
    <div v-if="user">
        <UserForm :user="user" update />
        <div class="totp">
            <EnableTotpForm :user="user" />
        </div>
    </div>
</template>

<style lang="scss" scoped>
.totp {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
