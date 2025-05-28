<script setup lang="ts">
import { watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

import api from '@/lib/api/api';
import { setCookie, getCookie } from '@/lib/cookies';
import { useEmitter } from '@/lib/eventBus';

const { locale } = useI18n();
const emitter = useEmitter();

const currentLocale = getCookie('locale') || 'fr';

onMounted(() => {
    if (currentLocale) {
        locale.value = currentLocale;
        api().setLocale(currentLocale);
    }
});

watch(locale, (newLocale: string) => {
    setCookie('locale', newLocale);
    api().setLocale(newLocale);

    emitter.emit('locale-changed', newLocale);
});
</script>

<template>
    <select v-model="$i18n.locale" class="locale-switcher">
        <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">
            {{ locale }}
        </option>
    </select>
</template>

<style scoped lang="scss">
.locale-switcher {
    position: absolute;
    top: 1.25rem;
    right: 2rem;
    height: 1.5rem;
}
</style>
