<script setup lang="ts">
import { ref } from 'vue';

import tokens from '@/i18n/tokens';
import api from '@/lib/api/api';

const message = ref('');
const messages = ref([
    {
        id: 1,
        content: 'Hello, how are you?',
    },
    {
        id: 2,
        content: 'I am fine, thank you!',
    },
    {
        id: 3,
        content: 'What about you?',
    },
]);

// setup mercure

const handleSubmit = async () => {
    if (!message.value) {
        return;
    }

    api().tchat().sendMessage(message.value);

    message.value = '';
};
</script>

<template>
    <div class="container">
        <h1>{{ $t(tokens.room.tchat.title) }}</h1>

        <div class="messages">
            <ul>
                <li v-for="message in messages" :key="message.id">{{ message.content }}</li>
            </ul>
        </div>

        <form @submit.prevent="handleSubmit">
            <input type="text" v-model="message" :placeholder="$t(tokens.room.tchat.placeholder)" />
            <button type="submit" class="submit-button">{{ $t(tokens.room.tchat.send) }}</button>
        </form>
    </div>
</template>

<style scoped lang="scss">
.container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;

    h1 {
        font-size: 2rem;
        color: #333;
    }

    .messages {
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

    form {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;

        .submit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 10px;

            &:hover {
                background-color: #0056b3;
            }
        }
    }
}
</style>
