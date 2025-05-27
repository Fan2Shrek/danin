<script setup lang="ts">
import { ref, onMounted } from 'vue';

import tokens from '@/i18n/tokens';
import api from '@/lib/api/api';
import { useEmitter } from '@/lib/eventBus';

import type { Game } from '@/lib/api/resources/game';

const emitter = useEmitter();

const games = ref<Game[]>([]);

const fetchGames = async () => {
    games.value = await api().game().getAll();
};

onMounted(() => {
    fetchGames();
});

emitter?.on('locale-changed', async () => {
    fetchGames();
});
</script>

<template>
    <div class="container">
        <div class="left">
            <h2>{{ $t(tokens.games.title) }}</h2>
            <p>{{ $t(tokens.games.subtitle) }}</p>

            <div class="games">
                <ul>
                    <li v-for="game in games" :key="game.id">
                        <strong>{{ game.id }}</strong
                        >: {{ game.name }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;

    .left {
        width: 50%;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-right: 20px;

        h2 {
            font-size: 2rem;
            color: #333;
        }

        p {
            font-size: 1rem;
            color: #666;
        }

        .games {
            margin-top: 20px;

            ul {
                list-style-type: none;
                padding: 0;

                li {
                    background-color: #fff;
                    padding: 10px;
                    margin-bottom: 10px;
                    border-radius: 5px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    color: black;
                }
            }
        }
    }
}
</style>
