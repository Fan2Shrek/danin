'use client';

import { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';

import { useAuth } from '@/context/AuthContext';
import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';
import UserBadge from '@/components/ui/UserBadge';
import LanguageSwitcher from '@/components/i18n/LanguageSwitcher';

export default function Navbar() {
    const { user } = useAuth();
    const { t } = useLocale();
    const [isOpen, setIsOpen] = useState(false);

    const links = [
        { label: t(tokens.navbar.links.games), href: '/games' },
        { label: t(tokens.navbar.links.tchat), href: '/' },
        { label: t(tokens.navbar.links.createRoom), href: '/room/create' },
    ];

    return (
        <nav className="fixed top-0 z-20 w-full border-b border-slate-200/80 bg-white/90 px-4 py-3 backdrop-blur-sm dark:border-slate-800 dark:bg-slate-900/90 sm:px-8">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <button
                        onClick={() => setIsOpen((v) => !v)}
                        aria-label="Toggle navigation"
                        aria-expanded={isOpen}
                        className="flex flex-col gap-1 p-2 md:hidden"
                    >
                        <span className="h-0.5 w-5 bg-slate-900 dark:bg-white" />
                        <span className="h-0.5 w-5 bg-slate-900 dark:bg-white" />
                        <span className="h-0.5 w-5 bg-slate-900 dark:bg-white" />
                    </button>
                    <Link href="/" className="flex items-center gap-2 text-xl font-bold text-slate-900 dark:text-white">
                        <Image src="/D.png" alt="Logo" width={36} height={36} />
                        Danin
                    </Link>
                </div>

                <ul className="hidden items-center gap-6 md:flex">
                    {links.map((link) => (
                        <li key={link.href}>
                            <Link
                                href={link.href}
                                className="text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400"
                            >
                                {link.label}
                            </Link>
                        </li>
                    ))}
                    {!user && (
                        <li>
                            <Link
                                href="/login"
                                className="text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400"
                            >
                                {t(tokens.navbar.links.login)}
                            </Link>
                        </li>
                    )}
                </ul>

                <div className="flex items-center gap-4">
                    <UserBadge />
                    <LanguageSwitcher />
                </div>
            </div>

            {isOpen && (
                <ul className="mt-3 flex flex-col gap-1 md:hidden">
                    {links.map((link) => (
                        <li key={link.href}>
                            <Link
                                href={link.href}
                                onClick={() => setIsOpen(false)}
                                className="block rounded-lg px-3 py-2 text-slate-600 hover:bg-indigo-50 dark:text-slate-300 dark:hover:bg-indigo-500/10"
                            >
                                {link.label}
                            </Link>
                        </li>
                    ))}
                    {!user && (
                        <li>
                            <Link
                                href="/login"
                                onClick={() => setIsOpen(false)}
                                className="block rounded-lg px-3 py-2 text-slate-600 hover:bg-indigo-50 dark:text-slate-300 dark:hover:bg-indigo-500/10"
                            >
                                {t(tokens.navbar.links.login)}
                            </Link>
                        </li>
                    )}
                </ul>
            )}
        </nav>
    );
}
