<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation } from 'swiper/modules';
import { useRouter } from 'vue-router';

import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';
import { useEmitter } from '@/lib/eventBus';
import BasicButton from '@/components/ui/BasicButton.vue';

import type { Swiper as SwiperClass } from 'swiper';
import type { Command } from '@/lib/api/resources/game';
import type { RoomConfig } from '@/lib/api/resources/room';
import type { Provider } from '@/lib/api/resources/provider';

type FieldKey = keyof typeof tokens.room.create.settings;

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const router = useRouter();
const emitter = useEmitter();

// todo api call
const games = ref([
    {
        id: 'tboi',
        name: 'The binding of Isaac',
        image: 'https://img.favpng.com/5/24/17/the-binding-of-isaac-afterbirth-plus-video-game-minecraft-png-favpng-6hzHSa2YLxqa861aZC8P9s5fU.jpg',
    },
    {
        id: 'tboi2',
        name: 'The binding of Isaac 2',
        image: 'https://studio.ican-design.fr/ecole-infographie/sites/3/2020/08/petitPas00.jpg',
    },
]);

// todo api call
const transports = ref({
    mercure: {
        id: 'mercure',
        name: 'Mercure',
        fields: [],
    },
    socket: {
        id: 'socket',
        name: 'Socket',
        fields: ['host', 'port'],
    },
});

const providers = ref<Provider[]>([]);

const currentFields = computed(() => {
    // @ts-expect-error et tout
    return transports.value[config.value.transport]?.fields || [];
});

const commands = ref<Command[]>([]);
const config = ref<RoomConfig>({
    game: games.value[0].id,
    commands: commands.value.map((command) => command.name),
    providers: providers.value.map((provider) => provider.name),
    transport: Object.values(transports.value)[0].id,
    config: {},
});

const onSlideChange = (swiper: SwiperClass) => {
    config.value.game = games.value[swiper.activeIndex].id;
};

const fetchCommands = async () => {
    commands.value = await api().game().getCommands('tboi');
    config.value.commands = commands.value.map((command) => command.name);
};

const fetchProviders = async () => {
    providers.value = await api().provider().getAll();
    config.value.providers = providers.value.map((provider) => provider.name);
};

const handleCheck = (key: 'commands' | 'providers', name: string): void => {
    const target = config.value[key] as string[];

    if (!target) {
        config.value[key] = [name];
    } else if (target.includes(name)) {
        config.value[key] = target.filter((id) => id !== name);
    } else {
        config.value[key].push(name);
    }
};

const handleChange = (e: Event, name: string) => {
    const input = e.target as HTMLInputElement;
    config.value.config[name] = input.value ?? null;
};

onMounted(() => {
    fetchCommands();
    fetchProviders();
});

emitter?.on('locale-changed', async () => {
    fetchCommands();
});

const handleSubmit = async () => {
    const response = await api().room().create(config.value);

    router.push({
        name: 'StartRoom',
        params: {
            id: response.id,
        },
    });
};
</script>

<template>
    <form @submit.prevent="handleSubmit">
        <div class="container">
            <div class="container__form">
                <h2>{{ $t(tokens.room.create.game.title) }}</h2>
                <swiper
                    class="swiper"
                    :slides-per-view="1"
                    :space-between="10"
                    :modules="[Navigation]"
                    @slideChange="onSlideChange"
                    navigation
                    loop
                >
                    <swiperSlide v-for="(game, key) in games" :key="key">
                        <div class="game-item">
                            <img :src="game.image" alt="Game Image" />
                            <h2>{{ game.name }}</h2>
                            <p>{{ $t(tokens.room.create.game.howTo, { gameName: game.name }) }}</p>
                            <!-- todo article -->
                            <router-link to="#">{{ $t(tokens.room.create.game.cta) }}</router-link>
                        </div>
                    </swiperSlide>
                </swiper>
            </div>

            <div class="container__form settings">
                <h2>{{ $t(tokens.room.create.settings.title) }}</h2>

                <div class="form__settings">
                    <div>
                        <div class="form-group">
                            <label>{{ $t(tokens.room.create.settings.transport) }}:</label>
                            <select
                                @change="
                                    (e) =>
                                        (config.transport =
                                            (e.target as HTMLSelectElement)?.value || '')
                                "
                                v-model="config.transport"
                            >
                                <option
                                    :value="transport.id"
                                    v-for="transport in transports"
                                    :key="transport.id"
                                >
                                    {{ transport.name }}
                                </option>
                            </select>
                        </div>
                        <div v-for="field in currentFields" :key="field" class="form-group">
                            <label>{{ $t(tokens.room.create.settings[field as FieldKey]) }}:</label>
                            <input
                                type="text"
                                @change="(e) => handleChange(e, field)"
                                v-model="config.config[field]"
                                class="form-input"
                            />
                        </div>
                    </div>
                    <BasicButton :text="$t(tokens.room.create.submit)" class="submit-button" />
                </div>
            </div>

            <div class="container__form commands">
                <h2>{{ $t(tokens.room.create.commands.title) }}</h2>
                <ul>
                    <li v-for="command in commands" :key="command.id">
                        <input
                            type="checkbox"
                            @change="() => handleCheck('commands', command.name)"
                            :id="command.id"
                            :checked="config.commands.includes(command.name)"
                        />
                        <label :for="command.id">!{{ command.name }}</label>
                    </li>
                </ul>
                <h2>{{ $t(tokens.room.create.providers.title) }}</h2>
                <ul>
                    <li v-for="provider in providers" :key="provider.name">
                        <input
                            type="checkbox"
                            @change="() => handleCheck('providers', provider.name)"
                            :id="provider.name"
                            :checked="config.providers.includes(provider.name)"
                        />
                        <label :for="provider.name">{{ provider.name }}</label>
                    </li>
                </ul>
            </div>
        </div>
    </form>
</template>

<style lang="scss" scoped>
.container {
    padding-top: 5rem;
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 1rem;
    height: 700px;

    &__form {
        width: 25%;
        height: 550px;
        background-color: #fff;
        border-radius: 0.5rem;
        color: #000;

        &.settings,
        &.commands {
            padding: 0 20px;
        }

        .form__settings {
            display: flex;
            height: 450px;
            flex-direction: column;
            justify-content: space-between;
        }

        .form-group {
            margin-bottom: 1rem;

            label {
                display: block;
                font-size: 1rem;
                margin-bottom: 0.5rem;
                color: #555;
            }

            input,
            select {
                width: 100%;
                padding: 0.5rem;
                border: 1px solid #ccc;
                border-radius: 0.25rem;
                font-size: 1rem;
            }
        }

        h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;

            li {
                display: flex;
                align-items: center;
                margin-bottom: 0.5rem;

                input {
                    margin-right: 0.5rem;
                }

                label {
                    font-size: 1rem;
                    color: #333;
                }
            }
        }

        .submit-button {
            width: 100%;
            margin-top: 1rem;
        }
    }

    .swiper {
        img {
            height: 300px;
            width: auto;
        }

        .game-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    }
}

@media (max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: center;
        height: auto;

        &__form {
            width: 90%;
            margin-bottom: 1rem;
        }
    }
}
</style>
