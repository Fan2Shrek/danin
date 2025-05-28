<script setup lang="ts">
import { RouterView } from 'vue-router';

import UserProvider from '@/components/UserProvider.vue';
import NavbarComponent from '@/components/layout/NavbarComponent.vue';
import FooterComponent from '@/components/layout/FooterComponent.vue';
</script>

<template>
    <UserProvider>
        <NavbarComponent />
        <RouterView />
        <FooterComponent />
    </UserProvider>
</template>
