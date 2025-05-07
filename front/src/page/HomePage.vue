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
            <BasicButton :text="$t(tokens.home.cta)" />
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
        }

        p {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
            color: #d0d0d0;
        }
    }
}
</style>
