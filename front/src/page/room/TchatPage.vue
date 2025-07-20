<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

import tokens from '@/i18n/tokens';
import api from '@/lib/api/api';
import mercure from '@/lib/mercure';
import { useEmitter } from '@/lib/eventBus';

import type { Message } from '@/lib/api/resources/tchat';
import type { Command } from '@/lib/api/resources/game';

const emitter = useEmitter();
const route = useRoute();

const roomId = route.params.id as string;
const message = ref('');
const messages = ref<Message[]>([]);

const commands = ref<Command[]>([]);

mercure.subscribe(`danin_tchat/${roomId}`, (data) => {
    const message = data as Message;
    messages.value.push(message);
});

onMounted(async () => {
    messages.value = await api().tchat().getMessages(roomId);
    messages.value.reverse();
});

const fetchCommands = async () => {
    commands.value = await api().room().getCommands(roomId);
};

onMounted(() => {
    fetchCommands();
});

emitter?.on('locale-changed', async () => {
    fetchCommands();
});

const handleSubmit = async () => {
    if (!message.value) {
        return;
    }

    api().tchat().sendMessage(message.value, roomId);

    window._paq.push(['trackEvent',
        'Tchat',
        'Message',
        'Message envoyé dans le tchat',
    ]);

    message.value = '';
};
</script>

<template>
    <div class="container">
        <div class="left">
            <h2>{{ $t(tokens.room.tchat.commands.title) }}</h2>
            <p>{{ $t(tokens.room.tchat.commands.description) }}</p>

            <div class="commands">
                <ul>
                    <li v-for="command in commands" :key="command.id">
                        <strong>{{ command.name }}</strong
                        >: {{ command.description }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="right">
            <h1>{{ $t(tokens.room.tchat.title) }}</h1>

            <div class="messages">
                <ul>
                    <li v-for="message in messages" :key="message['@id']">
                        <strong>{{ message.author }}</strong
                        >: {{ message.content }}
                    </li>
                </ul>
                <form @submit.prevent="handleSubmit">
                    <input
                        type="text"
                        v-model="message"
                        :placeholder="$t(tokens.room.tchat.placeholder)"
                    />
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.container {
    padding-top: 75px;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;

    .left {
        width: 50%;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-right: 20px;

        h2 {
            font-size: 2rem;
            color: #333;
        }

        p {
            font-size: 1rem;
            color: #666;
        }

        .commands {
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
    }

    .right {
        height: 90%;
        margin-top: 20px;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        h1 {
            font-size: 2rem;
            color: #333;
            text-align: center;
        }

        .messages {
            ul {
                list-style-type: none;
                padding: 0;

                li {
                    color: black;

                    small {
                        color: #999;
                        font-size: 0.8rem;
                        margin-right: 10px;
                    }
                }
            }
        }

        form {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 600px;
            margin: 10px auto;
        }
    }
}
</style>
