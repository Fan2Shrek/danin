<script setup lang="ts">
import { ref } from 'vue';

import tokens from '@/i18n/tokens';
import BasicButton from '@/components/ui/BasicButton.vue';

// todo replace with api call
const games = ref({
    game1: {
        name: 'Game 1',
        description: 'Description for Game 1',
        image: 'https://via.placeholder.com/150',
    },
    game2: {
        name: 'Game 2',
        description: 'Description for Game 2',
        image: 'https://via.placeholder.com/150',
    },
    game3: {
        name: 'Game 3',
        description: 'Description for Game 3',
        image: 'https://via.placeholder.com/150',
    },
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
            <div v-for="(game, key) in games" :key="key" class="game-item">
                <img :src="game.image" alt="Game Image" />
                <h2>{{ game.name }}</h2>
                <p>{{ game.description }}</p>
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
