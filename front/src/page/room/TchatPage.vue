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
                        <br />
                        <small>{{
                            new Date(message.sendAt).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                            })
                        }}</small>
                    </li>
                </ul>
            </div>

            <form @submit.prevent="handleSubmit">
                <input
                    type="text"
                    v-model="message"
                    :placeholder="$t(tokens.room.tchat.placeholder)"
                />
                <button type="submit" class="submit-button">
                    {{ $t(tokens.room.tchat.send) }}
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">
.container {
    min-height: 100vh;
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
}
</style>
