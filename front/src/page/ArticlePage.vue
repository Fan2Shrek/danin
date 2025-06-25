<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

import { useApiStore } from '@/stores/apiStore';

import type { Article } from '@/lib/api/resources/article';

const route = useRoute();
const apiStore = useApiStore();
const article = ref<Article | null>(null);

onMounted(async () => {
    article.value = await apiStore.getStoreState(`article_${route.params.slug}`);
});
</script>

<template>
    <div v-if="article" class="article-container">
        <h1>{{ article.title }}</h1>
        <div v-html="article.content" class="article-content"></div>
    </div>
</template>

<style scoped lang="scss">
.article-container {
    padding-top: 75px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #f9f9f9;
    color: #333;
    border: 1px solid #ddd;
}
</style>
