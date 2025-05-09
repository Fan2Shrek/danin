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
    <div class="locale-switcher">
        <select v-model="$i18n.locale">
            <option
                v-for="locale in $i18n.availableLocales"
                :key="`locale-${locale}`"
                :value="locale"
            >
                {{ locale }}
            </option>
        </select>
    </div>
</template>

<style scoped lang="scss">
.locale-switcher {
    position: fixed;
}
</style>
