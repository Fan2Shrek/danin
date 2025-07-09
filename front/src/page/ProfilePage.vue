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
        <EnableTotpForm :user="user" />
    </div>
</template>

<style lang="scss" scoped>
.page {
    padding-top: 75px;
    background-color: #f8f9fa;
    color: black;
}
</style>
