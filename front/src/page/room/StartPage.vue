<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';
import gameClient from '@/lib/gameClient';
import BasicButton from '@/components/ui/BasicButton.vue';

import type { LocalData } from '@/lib/gameClient';
import type { ProviderInfo } from '@/lib/api/resources/room';

const route = useRoute();
const router = useRouter();

const dots = ref('');
const error = ref<string | null>(null);
const text = ref(tokens.room.start.description);

let step = 0;
let interval: ReturnType<typeof setInterval>;

const routeId = route.params.id as string;
const isOk = ref(false);
const providers = ref<Record<string, ProviderInfo>>({});

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
            if (!response) {
                return;
            }

            if (!response.local_setup) {
                isOk.value = true;
            }
            providers.value = response.providers || {};

            text.value = tokens.room.start.requestConnectionToGame;

            const gameResponse = await gameClient().start(response.data as LocalData);

            if (gameResponse && gameResponse.status) {
                isOk.value = true;
            }
        })
        .catch((error) => {
            error.value = tokens.room.start.error;
            console.error('Error starting room:', error);
            clearInterval(interval);
        });
});

const handleCopy = (token: string) => {
    navigator.clipboard.writeText(token);
};

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<template>
    <div class="start-room">
        <div class="content">
            <h1>{{ $t(tokens.room.start.title) }}</h1>
            <p v-if="error" class="error">
                {{ $t(error) }}
            </p>
            <p v-else-if="!isOk">
                {{ $t(text) }}<span>{{ dots }}</span>
            </p>
            <div v-if="providers && Object.keys(providers).length > 0">
                <h2>{{ $t(tokens.room.start.providers) }}</h2>
                <ul>
                    <li v-for="(provider, name) in providers" :key="name">
                        <div>
                            <span>{{ name }}: </span>
                            <p>{{ $t(tokens.room.start.providerCommand) }}</p>
                            <code>{{ provider.command }}</code>
                        </div>
                        <button @click="handleCopy(provider.token)">
                            {{ $t(tokens.room.start.copyToken) }}
                        </button>
                    </li>
                </ul>
            </div>
            <BasicButton
                :text="$t(tokens.room.start.go)"
                @click="router.push({ name: 'Tchat', params: { id: routeId } })"
                v-if="isOk"
            />
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

        ul {
            list-style-type: none;
            padding: 0;
            margin: 1rem 0;

            li {
                margin: 0.5rem 0;

                div {
                    display: flex;
                    justify-content: space-between;
                    font-size: 1.1rem;

                    span {
                        color: #555;
                    }
                }
            }
        }

        code {
            background-color: #f0f0f0;
            padding: 0.5rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 1rem;
        }
    }
}
</style>
