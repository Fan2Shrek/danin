'use client';

import { useEffect, useMemo, useState } from 'react';
import { useRouter } from 'next/navigation';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation } from 'swiper/modules';
import type { Swiper as SwiperClass } from 'swiper';
import Image from 'next/image';
import Link from 'next/link';

import 'swiper/css';
import 'swiper/css/navigation';

import api from '@/lib/api/api';
import { getStoreState } from '@/lib/apiStore';
import { useLocale } from '@/context/LocaleContext';
import RequireAuth from '@/components/RequireAuth';
import tokens from '@/i18n/tokens';

import type { Command, Game, Transport } from '@/lib/api/resources/game';
import type { RoomConfig } from '@/lib/api/resources/room';
import type { Provider } from '@/lib/api/resources/provider';

type FieldKey = keyof typeof tokens.room.create.settings;

export default function CreateRoomPage() {
    return (
        <RequireAuth>
            <CreateRoomForm />
        </RequireAuth>
    );
}

function CreateRoomForm() {
    const router = useRouter();
    const { locale, t } = useLocale();

    const [games, setGames] = useState<Game[]>([]);
    const [transports, setTransports] = useState<Record<string, Transport>>({});
    const [providers, setProviders] = useState<Provider[]>([]);
    const [commands, setCommands] = useState<Command[]>([]);
    const [config, setConfig] = useState<RoomConfig>({
        game: '',
        transport: '',
        commands: [],
        providers: [],
        config: {},
    });

    const currentFields = useMemo(() => transports[config.transport]?.fields || [], [transports, config.transport]);

    useEffect(() => {
        (async () => {
            const gamesList = await getStoreState<Game[]>(locale, 'games');
            setGames(gamesList);
            if (gamesList[0]) {
                setConfig((c) => ({ ...c, game: gamesList[0].id }));
            }

            const transportsList = await getStoreState<Transport[]>(locale, 'transports');
            const byName = transportsList.reduce((acc: Record<string, Transport>, cur) => {
                acc[cur.name] = cur;

                return acc;
            }, {});
            setTransports(byName);
            const first = Object.values(byName)[0];
            if (first) {
                setConfig((c) => ({ ...c, transport: first.id }));
            }

            const providersList = await getStoreState<Provider[]>(locale, 'providers');
            setProviders(providersList);
            setConfig((c) => ({ ...c, providers: providersList.map((p) => p.id) }));
        })();
    }, [locale]);

    useEffect(() => {
        (async () => {
            if (!config.game) {
                return;
            }

            const cmds = await getStoreState<Command[]>(locale, `commands_${config.game}`);
            setCommands(cmds);
            setConfig((c) => ({ ...c, commands: cmds.map((cmd) => cmd.name) }));
        })();
    }, [locale, config.game]);

    const handleCheck = (key: 'commands' | 'providers', name: string) => {
        setConfig((c) => {
            const target = c[key];
            const next = target.includes(name) ? target.filter((id) => id !== name) : [...target, name];

            return { ...c, [key]: next };
        });
    };

    const onSlideChange = (swiper: SwiperClass) => {
        if (games.length === 0 || isNaN(swiper.activeIndex)) {
            return;
        }

        setConfig((c) => ({ ...c, game: games[swiper.activeIndex].id }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const response = await api().room().create(config);
        router.push(`/room/${response.id}/start`);
    };

    return (
        <form onSubmit={handleSubmit} className="mx-auto max-w-6xl px-4 py-10">
            <div className="flex flex-col gap-6 md:flex-row">
                <div className="rounded-2xl bg-white p-6 shadow-md dark:bg-slate-900 md:w-1/3">
                    <h2 className="mb-4 text-center text-2xl font-bold text-slate-900 dark:text-white">
                        {t(tokens.room.create.game.title)}
                    </h2>
                    <Swiper
                        modules={[Navigation]}
                        navigation
                        loop
                        slidesPerView={1}
                        spaceBetween={10}
                        onSlideChange={onSlideChange}
                    >
                        {games.map((game) => (
                            <SwiperSlide key={game.id}>
                                <div className="flex flex-col items-center gap-3 pb-8 text-center">
                                    {game.image && (
                                        <Image
                                            src={api().image(game.image)}
                                            alt={game.name}
                                            width={200}
                                            height={200}
                                            unoptimized
                                            className="rounded-xl"
                                        />
                                    )}
                                    <h3 className="text-xl font-semibold text-slate-900 dark:text-white">{game.name}</h3>
                                    {game.setupArticleSlug && (
                                        <div>
                                            <p className="text-sm text-slate-500 dark:text-slate-400">
                                                {t(tokens.room.create.game.howTo, { gameName: game.name })}
                                            </p>
                                            <Link
                                                href={`/article/${game.setupArticleSlug}`}
                                                className="font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                                            >
                                                {t(tokens.room.create.game.cta)}
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            </SwiperSlide>
                        ))}
                    </Swiper>
                </div>

                <div className="flex flex-col justify-between gap-4 rounded-2xl bg-white p-6 shadow-md dark:bg-slate-900 md:w-1/3">
                    <div>
                        <h2 className="mb-4 text-center text-2xl font-bold text-slate-900 dark:text-white">
                            {t(tokens.room.create.settings.title)}
                        </h2>
                        <div className="flex flex-col gap-4">
                            <div>
                                <label className="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">
                                    {t(tokens.room.create.settings.transport)}:
                                </label>
                                <select
                                    value={config.transport}
                                    onChange={(e) => setConfig((c) => ({ ...c, transport: e.target.value }))}
                                    className="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                    {Object.values(transports).map((transport) => (
                                        <option key={transport.id} value={transport.id}>
                                            {transport.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            {currentFields.map((field) => (
                                <div key={field}>
                                    <label className="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">
                                        {t(tokens.room.create.settings[field as FieldKey])}:
                                    </label>
                                    <input
                                        type="text"
                                        value={config.config[field] ?? ''}
                                        onChange={(e) =>
                                            setConfig((c) => ({
                                                ...c,
                                                config: { ...c.config, [field]: e.target.value },
                                            }))
                                        }
                                        className="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                    <button
                        type="submit"
                        className="rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-500"
                    >
                        {t(tokens.room.create.submit)}
                    </button>
                </div>

                <div className="rounded-2xl bg-white p-6 shadow-md dark:bg-slate-900 md:w-1/3">
                    <h2 className="mb-4 text-center text-2xl font-bold text-slate-900 dark:text-white">
                        {t(tokens.room.create.commands.title)}
                    </h2>
                    <ul className="mb-6 flex flex-col gap-2">
                        {commands.map((command) => (
                            <li key={command.id} className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    id={command.id}
                                    checked={config.commands.includes(command.name)}
                                    onChange={() => handleCheck('commands', command.name)}
                                />
                                <label htmlFor={command.id} className="text-slate-700 dark:text-slate-200">
                                    !{command.name}
                                </label>
                            </li>
                        ))}
                    </ul>
                    <h2 className="mb-4 text-center text-2xl font-bold text-slate-900 dark:text-white">
                        {t(tokens.room.create.providers.title)}
                    </h2>
                    <ul className="flex flex-col gap-2">
                        {providers.map((provider) => (
                            <li key={provider.id} className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    id={provider.id}
                                    checked={config.providers.includes(provider.id)}
                                    onChange={() => handleCheck('providers', provider.id)}
                                />
                                <label htmlFor={provider.id} className="text-slate-700 dark:text-slate-200">
                                    {provider.id}
                                </label>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </form>
    );
}
