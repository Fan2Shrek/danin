<script setup lang="ts">
import { ref, onMounted } from 'vue';

import api from '@/lib/api/api';
import { useApiStore } from '@/stores/apiStore';
import { useEmitter } from '@/lib/eventBus';
import tokens from '@/i18n/tokens';
import BasicButton from '@/components/ui/BasicButton.vue';
import GameCard from '@/components/ui/GameCard.vue';

import type { Game } from '@/lib/api/resources/game';
import type { Provider } from '@/lib/api/resources/provider';

const emitter = useEmitter();
const games = ref<Game[]>([]);
const providers = ref<Provider[]>([]);
const apiStore = useApiStore();

onMounted(async () => {
    games.value = await apiStore.getStoreState('games');
    providers.value = await apiStore.getStoreState('providers');
});

emitter?.on('locale-changed', async () => {
    games.value = await apiStore.getStoreState('games');
});
</script>

<template>
    <div class="home-container">
        <div class="home-header">
            <h1>{{ $t(tokens.home.title) }}</h1>
            <p>{{ $t(tokens.home.subtitle) }}</p>
            <BasicButton link="/room/create" :text="$t(tokens.home.cta)" class="home-header__btn" />
        </div>
        <div class="game-list">
            <h2>{{ $t(tokens.home.games.title) }}</h2>
            <GameCard v-for="game in games" :key="game.id" :game="game" />
        </div>
        <div class="providers-list">
            <h2>{{ $t(tokens.home.providers.title) }}</h2>
            <div v-for="provider in providers" :key="provider.id">
                <img v-if="provider.image" :src="api().image(provider.image)" :alt="provider.id" />
                <p>{{ provider.id }}</p>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.home-container {
    .home-header {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        height: 100vh;
        padding: 2rem;
        background:
            linear-gradient(to right, rgba(15, 15, 35, 0.8), rgba(15, 15, 35, 0.8)),
            url('/img/homeBanner.png') center/cover no-repeat;
        color: #ffffff;
        font-family: 'Helvetica Neue', sans-serif;

        h1 {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            animation: fadeInLeft 1s ease-out forwards;
        }

        p {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
            color: #d0d0d0;
            opacity: 0;
            animation: fadeInLeft 1s ease-out 0.5s forwards;
        }

        &__btn {
            opacity: 0;
            animation: fadeInUp 1s ease-out 1s forwards;
        }
    }

    .game-list {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-items: center;
        gap: 3em;
        justify-content: center;
        padding: 2rem;
    }

    .providers-list {
        display: flex;
        flex-wrap: wrap;
        flex-direction: column;
        align-items: center;
        gap: 3em;
        justify-content: center;
        padding: 2rem;
        background-color: #f0f0f0;

        h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
            color: #333;
        }

        div {
            margin: 0.5rem;
            padding: 1rem;
            background-color: #f8f8f8;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 1.2rem;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 150px;

            img {
                width: 100px;
                height: auto;
                border-radius: 0.5rem;
            }
        }
    }
}

@keyframes fadeInLeft {
    0% {
        opacity: 0;
        transform: translateX(-50px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .home-container {
        text-align: center;

        .home-header {
            align-items: center;
            h1 {
                font-size: 3.5rem;
            }
        }
    }
}
</style>
