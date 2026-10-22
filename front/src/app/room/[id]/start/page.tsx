'use client';

import { useEffect, useRef, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';

import api from '@/lib/api/api';
import gameClient from '@/lib/gameClient';
import RequireAuth from '@/components/RequireAuth';
import { useLocale } from '@/context/LocaleContext';
import BasicButton from '@/components/ui/BasicButton';
import tokens from '@/i18n/tokens';

import type { LocalData } from '@/lib/gameClient';
import type { ProviderInfo } from '@/lib/api/resources/room';

export default function StartRoomPage() {
    return (
        <RequireAuth>
            <StartRoom />
        </RequireAuth>
    );
}

function StartRoom() {
    const { id } = useParams<{ id: string }>();
    const router = useRouter();
    const { t } = useLocale();

    const [dots, setDots] = useState('');
    const [error, setError] = useState<string | null>(null);
    const [text, setText] = useState(tokens.room.start.description);
    const [isOk, setIsOk] = useState(false);
    const [providers, setProviders] = useState<Record<string, ProviderInfo>>({});
    const started = useRef(false);

    useEffect(() => {
        const cycle = ['.', '..', '...'];
        let step = 0;
        const interval = setInterval(() => {
            setDots(cycle[step % cycle.length]);
            step++;
        }, 500);

        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        if (started.current) {
            return;
        }
        started.current = true;

        (async () => {
            try {
                const response = await api().room().start(id);

                if (!response) {
                    return;
                }

                if (!response.local_setup) {
                    setIsOk(true);
                }

                setProviders(response.providers || {});
                setText(tokens.room.start.requestConnectionToGame);

                const gameResponse = await gameClient().start(response.data as LocalData);

                if (gameResponse && gameResponse.status) {
                    setIsOk(true);
                }
            } catch (err) {
                setError(tokens.room.start.error);
                console.error('Error starting room:', err);
            }
        })();
    }, [id]);

    const handleCopy = (token: string) => {
        navigator.clipboard.writeText(token);
    };

    return (
        <div className="flex min-h-[calc(100vh-4rem)] items-center justify-center px-4">
            <div className="w-full max-w-lg text-center">
                <h1 className="mb-4 text-3xl font-bold text-slate-900 dark:text-white">{t(tokens.room.start.title)}</h1>
                {error ? (
                    <p className="font-semibold text-red-500">{t(error)}</p>
                ) : (
                    !isOk && (
                        <p className="text-slate-600 dark:text-slate-300">
                            {t(text)}
                            <span>{dots}</span>
                        </p>
                    )
                )}

                {Object.keys(providers).length > 0 && (
                    <div className="mt-6">
                        <h2 className="mb-3 text-xl font-semibold text-slate-900 dark:text-white">
                            {t(tokens.room.start.providers)}
                        </h2>
                        <ul className="flex flex-col gap-3">
                            {Object.entries(providers).map(([name, provider]) => (
                                <li
                                    key={name}
                                    className="flex items-center justify-between gap-3 rounded-xl bg-white p-4 shadow-md dark:bg-slate-800"
                                >
                                    <div className="text-left">
                                        <span className="font-semibold text-slate-900 dark:text-white">{name}: </span>
                                        <p className="text-sm text-slate-500 dark:text-slate-400">
                                            {t(tokens.room.start.providerCommand)}
                                        </p>
                                        <code className="rounded bg-slate-100 px-2 py-1 text-sm dark:bg-slate-700">
                                            {provider.command}
                                        </code>
                                    </div>
                                    <button
                                        onClick={() => handleCopy(provider.token)}
                                        className="whitespace-nowrap rounded-lg bg-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-200"
                                    >
                                        {t(tokens.room.start.copyToken)}
                                    </button>
                                </li>
                            ))}
                        </ul>
                    </div>
                )}

                {isOk && (
                    <BasicButton
                        text={t(tokens.room.start.go)}
                        onClick={() => router.push(`/room/${id}/tchat`)}
                        className="mt-6"
                    />
                )}
            </div>
        </div>
    );
}
