<script setup lang="ts">
import { RouterView } from 'vue-router';

import LanguageSwitcher from '@/components/i18n/LanguageSwitcher.vue';
import FooterComponent from '@/components/layout/FooterComponent.vue';
</script>

<template>
    <LanguageSwitcher />
    <RouterView />
    <FooterComponent />
</template>
