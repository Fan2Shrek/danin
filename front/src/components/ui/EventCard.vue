<script setup lang="ts">
import type { Event } from '@/lib/api/resources/event';

defineProps<{
    event: Event;
}>();
</script>

<template>
    <a :href="event.link" target="_blank">
        <div class="event-card" role="article">
            <h2>{{ event.title }}</h2>
            <p v-if="event.startAt">{{ new Date(event.startAt).toLocaleDateString() }}</p>
        </div>
    </a>
</template>

<style scoped>
a {
    text-decoration: none;
}

.event-card {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    position: relative;
    text-align: center;
    color: #333;
}
</style>
