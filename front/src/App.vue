<script setup lang="ts">
import { RouterView } from 'vue-router';

import UserProvider from '@/components/UserProvider.vue';
import LanguageSwitcher from '@/components/i18n/LanguageSwitcher.vue';
import FooterComponent from '@/components/layout/FooterComponent.vue';
</script>

<template>
    <UserProvider>
        <LanguageSwitcher />
        <RouterView />
        <FooterComponent />
    </UserProvider>
</template>
