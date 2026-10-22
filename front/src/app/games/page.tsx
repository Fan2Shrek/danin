'use client';

import { useEffect, useState } from 'react';

import { getStoreState } from '@/lib/apiStore';
import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';
import GameCard from '@/components/ui/GameCard';

import type { Game } from '@/lib/api/resources/game';

export default function GamesPage() {
    const { locale, t } = useLocale();
    const [games, setGames] = useState<Game[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        (async () => {
            setIsLoading(true);
            try {
                setGames(await getStoreState<Game[]>(locale, 'games'));
            } finally {
                setIsLoading(false);
            }
        })();
    }, [locale]);

    return (
        <section className="bg-slate-50 px-6 py-16 dark:bg-slate-950 sm:px-16">
            <h1 className="text-center text-3xl font-bold text-slate-900 dark:text-white">{t(tokens.games.title)}</h1>
            <p className="mx-auto mt-2 max-w-xl text-center text-slate-500 dark:text-slate-400">
                {t(tokens.games.subtitle)}
            </p>

            {isLoading ? (
                <div className="flex justify-center py-16">
                    <div className="h-12 w-12 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600" />
                </div>
            ) : (
                <ul className="mx-auto mt-10 grid max-w-5xl grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {games.map((game) => (
                        <li key={game.id}>
                            <GameCard game={game} />
                        </li>
                    ))}
                </ul>
            )}
        </section>
    );
}
