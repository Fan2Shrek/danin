'use client';

import { createContext, useCallback, useContext, useEffect, useState } from 'react';

import api from '@/lib/api/api';
import { getCookie, setCookie } from '@/lib/cookies';
import fr from '@/i18n/fr';
import en from '@/i18n/en';

const messages: Record<string, Record<string, string>> = { fr, en };
export const availableLocales = Object.keys(messages);

type LocaleContextValue = {
    locale: string;
    setLocale: (locale: string) => void;
    t: (key: string, params?: Record<string, string>) => string;
};

const LocaleContext = createContext<LocaleContextValue | null>(null);

export function LocaleProvider({ children }: { children: React.ReactNode }) {
    const [locale, setLocaleState] = useState(() => {
        const current = getCookie('locale');

        return current && messages[current] ? current : 'fr';
    });

    useEffect(() => {
        api().setLocale(locale);
    }, [locale]);

    const setLocale = useCallback((newLocale: string) => {
        setLocaleState(newLocale);
        setCookie('locale', newLocale);
        api().setLocale(newLocale);
    }, []);

    const t = useCallback(
        (key: string, params?: Record<string, string>) => {
            let value = messages[locale]?.[key] ?? messages.en[key] ?? key;

            if (params) {
                for (const [param, replacement] of Object.entries(params)) {
                    value = value.replace(`{${param}}`, replacement);
                }
            }

            return value;
        },
        [locale],
    );

    return <LocaleContext.Provider value={{ locale, setLocale, t }}>{children}</LocaleContext.Provider>;
}

export function useLocale() {
    const context = useContext(LocaleContext);

    if (!context) {
        throw new Error('useLocale must be used within a LocaleProvider');
    }

    return context;
}
