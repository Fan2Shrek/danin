<script setup lang="ts">
import { RouterView } from 'vue-router'

import LanguageSwitcher from '@/components/i18n/LanguageSwitcher.vue'
</script>

<template>
    <LanguageSwitcher />
    <RouterView />
</template>
