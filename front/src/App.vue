<script setup lang="ts">
import { RouterView } from 'vue-router';

import UserProvider from '@/components/UserProvider.vue';
import Navbar from './components/layout/Navbar.vue';
import FooterComponent from '@/components/layout/FooterComponent.vue';
</script>

<template>
    <UserProvider>
        <Navbar />
        <RouterView />
        <FooterComponent />
    </UserProvider>
</template>
