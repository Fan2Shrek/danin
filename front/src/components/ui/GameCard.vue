<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Game } from '@/lib/api/resources/game';

defineProps<{game: Game}>();

const isVisible = ref(false);
const cardRef = ref<HTMLElement | null>(null);

onMounted(() => {
    // Add a small delay for animation effect
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <div
        ref="cardRef"
        class="game-card"
        :class="{ visible: isVisible }"
        role="article"
    >
        <span class="game-code">{{ game.id }}</span>
        <h2>{{ game.name }}</h2>
        <p class="game-description">{{ game.description }}</p>
    </div>
</template>

<style scoped>
.game-card {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    transform: translateY(20px);
    opacity: 0;
    position: relative;
    text-align: center;
    padding: 1.5rem;
    height: 100%;
}

.game-card.visible {
    transform: translateY(0);
    opacity: 1;
}

.game-card:hover,
.game-card:focus {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
}

.game-card:focus {
    outline: 2px solid #646cff;
    outline-offset: 2px;
}

.game-code {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
    display: block;
}

h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.game-description {
    font-size: 1rem;
    color: #475569;
    line-height: 1.5;
}
</style>
