'use client';

import { availableLocales, useLocale } from '@/context/LocaleContext';

export default function LanguageSwitcher() {
    const { locale, setLocale } = useLocale();

    return (
        <select
            value={locale}
            onChange={(e) => setLocale(e.target.value)}
            className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
        >
            {availableLocales.map((l) => (
                <option key={l} value={l}>
                    {l}
                </option>
            ))}
        </select>
    );
}
