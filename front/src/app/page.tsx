'use client';

import { useEffect, useState } from 'react';
import Image from 'next/image';

import api from '@/lib/api/api';
import { getStoreState } from '@/lib/apiStore';
import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';
import BasicButton from '@/components/ui/BasicButton';
import GameCard from '@/components/ui/GameCard';
import EventCard from '@/components/ui/EventCard';

import type { Game } from '@/lib/api/resources/game';
import type { Provider } from '@/lib/api/resources/provider';
import type { Event } from '@/lib/api/resources/event';

export default function HomePage() {
    const { locale, t } = useLocale();
    const [games, setGames] = useState<Game[]>([]);
    const [providers, setProviders] = useState<Provider[]>([]);
    const [events, setEvents] = useState<Event[]>([]);

    useEffect(() => {
        (async () => {
            setGames(await getStoreState<Game[]>(locale, 'games'));
            setEvents(await getStoreState<Event[]>(locale, 'events'));
            setProviders(await getStoreState<Provider[]>(locale, 'providers'));
        })();
    }, [locale]);

    return (
        <div>
            <section
                className="relative flex min-h-[calc(100vh-4rem)] flex-col items-center justify-center px-6 text-center text-white sm:items-start sm:text-left sm:px-16"
                style={{
                    backgroundImage:
                        'linear-gradient(to right, rgba(15,15,35,0.85), rgba(15,15,35,0.85)), url(/homeBanner.png)',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                }}
            >
                <h1 className="text-4xl font-extrabold sm:text-6xl">{t(tokens.home.title)}</h1>
                <p className="mt-4 max-w-xl text-lg text-slate-300 sm:text-xl">{t(tokens.home.subtitle)}</p>
                <BasicButton link="/room/create" text={t(tokens.home.cta)} className="mt-8" />
            </section>

            <section className="px-6 py-16 text-center sm:px-16">
                <h2 className="mb-8 text-3xl font-bold text-slate-900 dark:text-white">{t(tokens.home.games.title)}</h2>
                <div className="flex flex-wrap justify-center gap-6">
                    {games.map((game) => (
                        <div key={game.id} className="w-64">
                            <GameCard game={game} description={false} />
                        </div>
                    ))}
                </div>
            </section>

            <section className="bg-slate-50 px-6 py-16 text-center dark:bg-slate-900 sm:px-16">
                <h2 className="mb-8 text-3xl font-bold text-slate-900 dark:text-white">
                    {t(tokens.home.providers.title)}
                </h2>
                <div className="flex flex-wrap justify-center gap-6">
                    {providers.map((provider) => (
                        <div
                            key={provider.id}
                            className="flex w-36 flex-col items-center gap-2 rounded-xl bg-white p-4 shadow-md dark:bg-slate-800"
                        >
                            {provider.image && (
                                <Image
                                    src={api().image(provider.image)}
                                    alt={provider.id}
                                    width={80}
                                    height={80}
                                    unoptimized
                                    className="rounded-lg"
                                />
                            )}
                            <p className="text-slate-700 dark:text-slate-200">{provider.id}</p>
                        </div>
                    ))}
                </div>
            </section>

            {events.length > 0 && (
                <section className="px-6 py-16 text-center sm:px-16">
                    <h2 className="mb-8 text-3xl font-bold text-slate-900 dark:text-white">
                        {t(tokens.home.events.title)}
                    </h2>
                    <div className="flex flex-wrap justify-center gap-6">
                        {events.map((event) => (
                            <div key={event.id} className="w-64">
                                <EventCard event={event} />
                            </div>
                        ))}
                    </div>
                </section>
            )}
        </div>
    );
}
