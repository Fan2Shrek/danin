<script setup lang="ts">
defineProps(['text', 'link']);
</script>

<template>
    <router-link v-if="link" :to="link" rel="noopener noreferrer">
        <button>
            {{ text }}
        </button>
    </router-link>
    <button v-else>
        {{ text }}
    </button>
</template>

<style scoped lang="scss">
button {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: bold;
    color: #ffffff;
    background-color: #3b82f6;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;

    a,
    span {
        color: #ffffff;
        text-decoration: none;
    }

    &:hover {
        background-color: #2563eb;
    }
}
</style>
