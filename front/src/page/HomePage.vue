<script setup lang="ts">
import { ref, onMounted } from 'vue';

import { useApiStore } from '@/stores/apiStore';
import { useEmitter } from '@/lib/eventBus';
import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';
import BasicButton from '@/components/ui/BasicButton.vue';

import type { Game } from '@/lib/api/resources/game';

const emitter = useEmitter();
const games = ref<Game[]>([]);
const apiStore = useApiStore();

onMounted(async () => {
    games.value = await apiStore.getStoreState('games');
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
            <div v-for="game in games" :key="game.id" class="game-item">
                <h2>{{ game.name }}</h2>
                <p>{{ game.description }}</p>
                <img v-if="game.image" :src="api().image(game.image)" alt="Game Image" />
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
        flex-wrap: wrap;
        justify-content: center;
        padding: 2rem;

        .game-item {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 1rem;
            padding: 1.5rem;
            width: calc(33.333% - 2rem);
            text-align: center;
            transition: transform 0.3s ease;

            h2 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            p {
                font-size: 1rem;
                color: #666666;
                margin-bottom: 1rem;
            }

            img {
                max-width: 100%;
                height: auto;
                border-radius: 4px;
            }

            &:hover {
                transform: translateY(-5px);
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
