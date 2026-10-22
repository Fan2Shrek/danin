'use client';

import { useState } from 'react';
import Link from 'next/link';

import { useAuth } from '@/context/AuthContext';
import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';

export default function LoginPage() {
    const { loginUser, verifyCode } = useAuth();
    const { t } = useLocale();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [totpCode, setTotpCode] = useState('');
    const [step, setStep] = useState<1 | 2>(1);
    const [errorMessage, setErrorMessage] = useState('');

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!email || !password) {
            setErrorMessage(tokens.login.error.empty);

            return;
        }

        try {
            setErrorMessage('');
            const response = await loginUser(email, password);

            if (response.need_totp) {
                setStep(2);
            }
        } catch {
            setErrorMessage(tokens.login.error.invalid);
        }
    };

    const handleCode = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!totpCode) {
            setErrorMessage(tokens.login.error.empty);

            return;
        }

        try {
            setErrorMessage('');
            await verifyCode(totpCode);
        } catch {
            setErrorMessage(tokens.login.totp.error);
        }
    };

    return (
        <div className="flex min-h-[calc(100vh-4rem)] items-center justify-center bg-slate-50 p-4 dark:bg-slate-950">
            <div className="w-full max-w-sm rounded-2xl bg-white p-8 shadow-lg dark:bg-slate-900">
                {step === 1 ? (
                    <>
                        <h1 className="mb-6 text-center text-2xl font-bold text-slate-900 dark:text-white">
                            {t(tokens.login.title)}
                        </h1>
                        <form onSubmit={handleLogin} className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    {t(tokens.login.email)}
                                </label>
                                <input
                                    type="text"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    className="rounded-lg border-2 border-slate-200 px-3 py-2 text-slate-900 outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    {t(tokens.login.password)}
                                </label>
                                <input
                                    type="password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    className="rounded-lg border-2 border-slate-200 px-3 py-2 text-slate-900 outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>
                            {errorMessage && (
                                <p className="text-center text-sm font-medium text-red-500">{t(errorMessage)}</p>
                            )}
                            <button
                                type="submit"
                                className="mt-2 rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-500"
                            >
                                {t(tokens.login.submit)}
                            </button>
                            <p className="text-center text-sm text-slate-500 dark:text-slate-400">
                                {t(tokens.login.register.link)}{' '}
                                <Link href="/register" className="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                                    {t(tokens.login.register.cta)}
                                </Link>
                            </p>
                        </form>
                    </>
                ) : (
                    <>
                        <h1 className="mb-6 text-center text-2xl font-bold text-slate-900 dark:text-white">
                            {t(tokens.login.totp.title)}
                        </h1>
                        <form onSubmit={handleCode} className="flex flex-col gap-5">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    {t(tokens.login.totp.input)}
                                </label>
                                <input
                                    type="text"
                                    value={totpCode}
                                    onChange={(e) => setTotpCode(e.target.value)}
                                    className="rounded-lg border-2 border-slate-200 px-3 py-2 text-slate-900 outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>
                            <button
                                type="submit"
                                className="mt-2 rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-500"
                            >
                                {t(tokens.login.totp.submit)}
                            </button>
                            {errorMessage && (
                                <p className="text-center text-sm font-medium text-red-500">{t(errorMessage)}</p>
                            )}
                        </form>
                    </>
                )}
            </div>
        </div>
    );
}
