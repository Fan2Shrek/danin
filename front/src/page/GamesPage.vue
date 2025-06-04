<script setup lang="ts">
import { ref, onMounted } from 'vue';

import tokens from '@/i18n/tokens';
import api from '@/lib/api/api';
import { useEmitter } from '@/lib/eventBus';
import GameCard from '@/components/ui/GameCard.vue';

import type { Game } from '@/lib/api/resources/game';

const emitter = useEmitter();

const games = ref<Game[]>([]);
const isLoading = ref(true);

const fetchGames = async () => {
    games.value = await api().game().getAll();
    isLoading.value = false;
};

onMounted(() => {
    fetchGames();
});

emitter?.on('locale-changed', async () => {
    fetchGames();
});
</script>

<template>
    <section class="container">
        <h1>{{ $t(tokens.games.title) }}</h1>
        <p class="subtitle">{{ $t(tokens.games.subtitle) }}</p>

        <div v-if="isLoading" class="loading-container">
            <div class="loading-spinner" aria-hidden="true"></div>
        </div>

        <ul v-else class="game-list" role="list" :aria-label="$t(tokens.games.subtitle)">
            <li v-for="game in games" :key="game.id" role="listitem">
                <GameCard :game="game" />
            </li>
        </ul>
    </section>
</template>

<style scoped lang="scss">
.container {
    width: 100%;
    padding: 5rem 2rem 2rem;
    background-color: #f8fafc;
}

h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    text-align: center;
}

.subtitle {
    font-size: 1.125rem;
    color: #475569;
    text-align: center;
    margin-bottom: 2rem;
}

.game-list {
    list-style: none;
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    padding: 0;
    margin: 0;
}

/* Tablet */
@media (min-width: 768px) {
    .container {
        padding-inline: 4rem;
    }

    .game-list {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .game-list {
        grid-template-columns: repeat(3, 1fr);
    }
}

.loading-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}

.loading-spinner {
    width: 3rem;
    height: 3rem;
    border: 4px solid rgba(100, 108, 255, 0.2);
    border-radius: 50%;
    border-top-color: #646cff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
