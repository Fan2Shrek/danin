<script setup lang="ts">
import { ref } from 'vue';
import QrcodeVue from 'qrcode.vue';

import tokens from '@/i18n/tokens';
import BasicButton from '../ui/BasicButton.vue';
import type { User } from '@/lib/api/resources/user';
import api from '@/lib/api/api';

const activate = ref(false);
const qrCodeUrl = ref<string | null>(null);

const { user } = defineProps<{
    user: User;
}>();

const handleActivate = async () => {
    const response = await api().user().activateTotp();

    activate.value = true;
    qrCodeUrl.value = response.qrCodeUrl;
};
</script>

<template>
    <div v-if="!user.totp" class="page">
        <div v-if="!activate">
            <BasicButton :text="$t(tokens.totp.enable)" @click="handleActivate" />
        </div>
        <div v-else>
            <p>
                {{ $t(tokens.totp.description) }}
                <qrcode-vue v-if="qrCodeUrl" :value="qrCodeUrl" />
            </p>
        </div>
    </div>
</template>
