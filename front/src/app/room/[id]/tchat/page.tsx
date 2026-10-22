'use client';

import { useEffect, useRef, useState } from 'react';
import { useParams } from 'next/navigation';

import api from '@/lib/api/api';
import mercure from '@/lib/mercure';
import RequireAuth from '@/components/RequireAuth';
import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';

import type { Message } from '@/lib/api/resources/tchat';
import type { Command } from '@/lib/api/resources/game';

export default function TchatPage() {
    return (
        <RequireAuth>
            <Tchat />
        </RequireAuth>
    );
}

function Tchat() {
    const { id } = useParams<{ id: string }>();
    const { locale, t } = useLocale();

    const [message, setMessage] = useState('');
    const [messages, setMessages] = useState<Message[]>([]);
    const [commands, setCommands] = useState<Command[]>([]);
    const subscribed = useRef(false);

    useEffect(() => {
        if (subscribed.current) {
            return;
        }
        subscribed.current = true;

        let eventSource: EventSource | undefined;
        mercure.subscribe(`danin_tchat/${id}`, (data) => {
            setMessages((prev) => [...prev, data as Message]);
        }).then((es) => {
            eventSource = es;
        });

        return () => eventSource?.close();
    }, [id]);

    useEffect(() => {
        (async () => {
            const initial = await api().tchat().getMessages(id);
            setMessages(initial.reverse());
        })();
    }, [id]);

    useEffect(() => {
        (async () => {
            setCommands(await api().room().getCommands(id));
        })();
    }, [id, locale]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (!message) {
            return;
        }

        api().tchat().sendMessage(message, id);
        setMessage('');
    };

    return (
        <div className="flex min-h-[calc(100vh-4rem)] flex-col items-center gap-4 bg-slate-50 px-4 py-8 dark:bg-slate-950 md:flex-row md:items-stretch md:justify-center">
            <aside className="w-full rounded-2xl bg-white p-6 shadow-md dark:bg-slate-900 md:w-1/3">
                <h2 className="mb-2 text-xl font-bold text-slate-900 dark:text-white">
                    {t(tokens.room.tchat.commands.title)}
                </h2>
                <p className="mb-4 text-sm text-slate-500 dark:text-slate-400">
                    {t(tokens.room.tchat.commands.description)}
                </p>
                <ul className="flex flex-col gap-2">
                    {commands.map((command) => (
                        <li key={command.id} className="rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800">
                            <strong className="text-slate-900 dark:text-white">{command.name}</strong>
                            <span className="text-slate-600 dark:text-slate-300">: {command.description}</span>
                        </li>
                    ))}
                </ul>
            </aside>

            <section className="flex w-full flex-col rounded-2xl bg-white p-6 shadow-md dark:bg-slate-900 md:w-1/2">
                <h1 className="mb-4 text-center text-2xl font-bold text-slate-900 dark:text-white">
                    {t(tokens.room.tchat.title)}
                </h1>
                <ul className="mb-4 flex-1 space-y-2 overflow-y-auto">
                    {messages.map((msg) => (
                        <li key={msg['@id']} className="text-slate-700 dark:text-slate-200">
                            <strong className="text-slate-900 dark:text-white">{msg.author}</strong>: {msg.content}
                        </li>
                    ))}
                </ul>
                <form onSubmit={handleSubmit}>
                    <input
                        type="text"
                        value={message}
                        onChange={(e) => setMessage(e.target.value)}
                        placeholder={t(tokens.room.tchat.placeholder)}
                        className="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    />
                </form>
            </section>
        </div>
    );
}
