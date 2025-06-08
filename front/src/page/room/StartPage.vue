<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';
import gameClient from '@/lib/gameClient';

const route = useRoute();
const router = useRouter();

const dots = ref('');
const error = ref(null);
const text = ref(tokens.room.start.description);

let step = 0;
let interval = null;

const routeId = route.params.id as string;

onMounted(() => {
    interval = setInterval(() => {
        const cycle = ['.', '..', '...'];
        dots.value = cycle[step % cycle.length];
        step++;
    }, 500);

    api()
        .room()
        .start(routeId)
        .then(async (response) => {
            if (response && response.ok) {
                router.push({ name: 'Tchat', params: { id: routeId } });

                return;
            }

            text.value = tokens.room.start.requestConnectionToGame;

            if (!response.local_setup) {
                error.value = tokens.room.start.localSetupError;
            }

            const gameResponse = await gameClient().start(response.data);

            if (gameResponse && gameResponse.ok) {
                router.push({ name: 'Tchat', params: { id: routeId } });
            }
        })
        .catch((error) => {
            error.value = tokens.room.start.error;
            console.error('Error starting room:', error);
            clearInterval(interval);
            router.push({ name: 'Error', params: { message: error.value } });
        });
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<template>
    <div class="start-room">
        <div class="content">
            <h1>{{ $t(tokens.room.start.title) }}</h1>
            <p v-if="!error">
                {{ $t(text) }}<span>{{ dots }}</span>
            </p>
            <p v-else class="error">{{ $t(error) }}</p>
        </div>
    </div>
</template>

<style scoped lang="scss">
.start-room {
    padding-top: 75px;
    display: flex;
    justify-content: center;
    height: 100vh;

    .content {
        text-align: center;
        width: 75%;
        background-color: #f9f9f9;
        color: #333;
        display: flex;
        flex-direction: column;
        justify-content: center;

        .error {
            color: red;
            font-weight: bold;
        }
    }
}
</style>
