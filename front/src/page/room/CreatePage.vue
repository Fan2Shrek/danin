<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation } from 'swiper/modules';
import { useRouter } from 'vue-router';

import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';
import { useApiStore } from '@/stores/apiStore';
import { useEmitter } from '@/lib/eventBus';
import BasicButton from '@/components/ui/BasicButton.vue';

import type { Swiper as SwiperClass } from 'swiper';
import type { Command, Game, Transport } from '@/lib/api/resources/game';
import type { RoomConfig } from '@/lib/api/resources/room';
import type { Provider } from '@/lib/api/resources/provider';

type FieldKey = keyof typeof tokens.room.create.settings;

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const router = useRouter();
const emitter = useEmitter();

const games = ref<Game[]>([]);
const apiStore = useApiStore();

const transports = ref<Record<string, Transport>>({});

const providers = ref<Provider[]>([]);

const currentFields = computed(() => {
    return transports.value[config.value.transport]?.fields || [];
});

const commands = ref<Command[]>([]);
const config = ref<RoomConfig>({
    // @ts-expect-error temporary
    game: null,
    commands: commands.value.map((command) => command.name),
    providers: providers.value.map((provider) => provider.id),
    // @ts-expect-error temporary
    transport: null,
    config: {},
});

const fetchCommands = async () => {
    if (!config.value.game) return;
    commands.value = await apiStore.getStoreState(`commands_${config.value.game}`);

    config.value.commands = commands.value.map((command) => command.name);
};

const fetchProviders = async () => {
    providers.value = await apiStore.getStoreState('providers');
    config.value.providers = providers.value.map((provider) => provider.id);
};

const fetchGames = async () => {
    games.value = await apiStore.getStoreState('games');

    config.value.game = games.value[0].id;
};

const fetchTransports = async () => {
    const transportsList = await apiStore.getStoreState('transports');

    transports.value = transportsList.reduce((acc: Record<string, Transport>, cur: Transport) => {
        acc[cur.name] = cur;

        return acc;
    }, {});

    config.value.transport = Object.values(transports.value)[0].id;
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

const onSlideChange = (swiper: SwiperClass) => {
    if (games.value.length === 0 || isNaN(swiper.activeIndex)) return;
    config.value.game = games.value[swiper.activeIndex].id;
};

onMounted(async () => {
    await fetchGames();
    fetchTransports();
    fetchCommands();
    fetchProviders();
});

emitter?.on('locale-changed', async () => {
    await fetchGames();
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

    window._paq.push(['trackEvent',
        'Transport',
        'Choix',
        'Transport choisi',
        config.value.transport
    ]);

    window._paq.push(['trackEvent',
        'Jeux',
        'Choix',
        'Jeu choisi',
        config.value.game
    ]);

    config.value.providers.forEach((provider) => {
        window._paq.push(['trackEvent',
            'Tchat',
            'Choix',
            'Système de tchat externe choisi',
            provider
        ]);
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
                            <img
                                v-if="game.image"
                                :src="api().image(game.image)"
                                alt="Game Image"
                            />
                            <h2>{{ game.name }}</h2>
                            <div v-if="game.setupArticleSlug">
                                <p>
                                    {{ $t(tokens.room.create.game.howTo, { gameName: game.name }) }}
                                </p>
                                <router-link
                                    :to="{
                                        name: 'Article',
                                        params: { slug: game.setupArticleSlug },
                                    }"
                                    >{{ $t(tokens.room.create.game.cta) }}</router-link
                                >
                            </div>
                        </div>
                    </swiperSlide>
                </swiper>
            </div>

            <div class="container__form settings">
                <div class="form__settings">
                    <h2>{{ $t(tokens.room.create.settings.title) }}</h2>
                    <div class="form-group">
                        <label>{{ $t(tokens.room.create.settings.transport) }}:</label>
                        <select
                            @change="
                                (e) =>
                                    (config.transport =
                                        (e.target as HTMLSelectElement)?.value || '')
                            "
                            v-model="config.transport"
                            v-if="transports"
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
                    <li v-for="provider in providers" :key="provider.id">
                        <input
                            type="checkbox"
                            @change="() => handleCheck('providers', provider.id)"
                            :id="provider.id"
                            :checked="config.providers.includes(provider.id)"
                        />
                        <label :for="provider.id">{{ provider.id }}</label>
                    </li>
                </ul>
            </div>
        </div>
    </form>
</template>

<style lang="scss" scoped>
.container {
    padding-top: 4rem;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;
    gap: 1rem;
    height: auto;

    &__form {
        width: 90%;
        height: 400px;
        background-color: #fff;
        border-radius: 0.5rem;
        color: #000;
        margin: 2rem auto;

        &.settings,
        &.commands {
            padding: 0 20px;
        }

        &.settings {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-bottom: 20px;
        }

        .form-group {
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
            margin: 1rem 0;
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
    }

    .swiper {
        img {
            height: 12rem;
            width: 12rem;
        }

        .game-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    }

    .submit-button {
        margin-top: 1rem;
    }
}

@media (min-width: 480px) {
    .swiper img {
        height: 300px;
        width: 300px;
    }
}

@media (min-width: 768px) {
    .container {
        flex-direction: row;
        height: 700px;

        &__form {
            width: 25%;
            height: 550px;
            margin: 0;
        }
    }
}
</style>
