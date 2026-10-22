'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';

import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';
import { getStoreState } from '@/lib/apiStore';

import type { Room } from '@/lib/api/resources/room';

export default function Footer() {
    const { locale, t } = useLocale();
    const [suggestGame, setSuggestGame] = useState<string | null>(null);
    const [setupGame, setSetupGame] = useState<string | null>(null);
    const [setupTchat, setSetupTchat] = useState<string | null>(null);
    const [rooms, setRooms] = useState<Room[]>([]);

    useEffect(() => {
        (async () => {
            const list = await getStoreState<{
                suggestGameArticleSlug?: string;
                setupGameArticleSlug?: string;
                setupTchatArticleSlug?: string;
            }>(locale, 'articlesList');
            setSuggestGame(list.suggestGameArticleSlug ?? null);
            setSetupGame(list.setupGameArticleSlug ?? null);
            setSetupTchat(list.setupTchatArticleSlug ?? null);
            setRooms(await getStoreState<Room[]>(locale, 'currentRooms'));
        })();
    }, [locale]);

    return (
        <footer className="mt-auto bg-slate-50 text-slate-700 dark:bg-slate-900 dark:text-slate-300">
            <div className="mx-auto flex max-w-6xl flex-col gap-10 px-6 py-12 text-center sm:flex-row sm:justify-between sm:text-left">
                <div>
                    <h3 className="mb-3 text-lg font-semibold text-slate-900 dark:text-white">
                        {t(tokens.footer.links.games.title)}
                    </h3>
                    <ul className="flex flex-col gap-2">
                        <li>
                            <Link href="/games" className="hover:underline">
                                {t(tokens.footer.links.games.supportedList)}
                            </Link>
                        </li>
                        <li>
                            <Link href={suggestGame ? `/article/${suggestGame}` : '#'} className="hover:underline">
                                {t(tokens.footer.links.games.suggestion)}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 className="mb-3 text-lg font-semibold text-slate-900 dark:text-white">
                        {t(tokens.footer.links.documentation.title)}
                    </h3>
                    <ul className="flex flex-col gap-2">
                        <li>
                            <Link href="/room/create" className="hover:underline">
                                {t(tokens.footer.links.documentation.createRoom)}
                            </Link>
                        </li>
                        <li>
                            <Link href={setupGame ? `/article/${setupGame}` : '#'} className="hover:underline">
                                {t(tokens.footer.links.documentation.connectGame)}
                            </Link>
                        </li>
                        <li>
                            <Link href={setupTchat ? `/article/${setupTchat}` : '#'} className="hover:underline">
                                {t(tokens.footer.links.documentation.connectTchat)}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 className="mb-3 text-lg font-semibold text-slate-900 dark:text-white">
                        {t(tokens.footer.links.currentGames.title)}
                    </h3>
                    <ul className="flex flex-col gap-2">
                        {rooms.map((room) => (
                            <li key={room.id}>
                                <Link href={`/room/${room.id}/tchat`} className="hover:underline">
                                    {room.owner.username} - {room.roomConfig.game}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </div>

                <div className="flex flex-col items-center gap-3 sm:items-start">
                    <h3 className="text-lg font-semibold text-slate-900 dark:text-white">
                        {t(tokens.footer.support.title)}
                    </h3>
                    <div className="flex gap-4">
                        <a href="https://github.com/Fan2Shrek/danin" target="_blank" rel="noopener noreferrer">
                            <Image src="/github-mark.svg" alt="Github" width={28} height={28} />
                        </a>
                        <Image src="/paypal.svg" alt="Paypal" width={28} height={28} />
                    </div>
                </div>
            </div>
            <div className="border-t border-slate-200 py-4 text-center text-sm dark:border-slate-800">
                <p>
                    {t(tokens.footer.copyright)} · © Danin {new Date().getFullYear()}
                </p>
            </div>
        </footer>
    );
}
