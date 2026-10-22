'use client';

import { useState } from 'react';
import Link from 'next/link';

import { useAuth } from '@/context/AuthContext';
import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';

export default function UserBadge() {
    const { user, logoutUser } = useAuth();
    const { t } = useLocale();
    const [open, setOpen] = useState(false);

    if (!user) {
        return null;
    }

    return (
        <div className="relative">
            <button
                onClick={() => setOpen((v) => !v)}
                className="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300"
            >
                {user.username.charAt(0).toUpperCase()}
            </button>
            {open && (
                <div className="absolute right-0 top-full mt-2 min-w-[11rem] rounded-xl border border-slate-200 bg-white p-3 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <div className="mb-2 border-b border-slate-100 pb-2 text-center dark:border-slate-700">
                        <p className="font-medium capitalize text-slate-900 dark:text-white">{user.username}</p>
                        <p className="text-sm text-slate-500 dark:text-slate-400">{user.email}</p>
                    </div>
                    <div className="flex flex-col gap-1">
                        <Link
                            href="/profile"
                            onClick={() => setOpen(false)}
                            className="rounded-lg px-2 py-1 text-sm text-indigo-600 hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                        >
                            {t(tokens.navbar.links.profile)}
                        </Link>
                        <button
                            onClick={() => {
                                setOpen(false);
                                logoutUser();
                            }}
                            className="rounded-lg px-2 py-1 text-left text-sm text-indigo-600 hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                        >
                            {t(tokens.navbar.links.logout)}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
