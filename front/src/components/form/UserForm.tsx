'use client';

import { useState } from 'react';
import Link from 'next/link';
import { z } from 'zod';

import { usePasswordStrength } from '@/hooks/usePasswordStrength';
import { useLocale } from '@/context/LocaleContext';
import api from '@/lib/api/api';
import tokens from '@/i18n/tokens';

type Props = {
    user?: { username: string; email: string };
    update?: boolean;
};

export default function UserForm({ user, update = false }: Props) {
    const { t } = useLocale();
    const mode = update ? 'update' : 'register';

    const { password, setPassword, strengthResult, isPasswordStrong } = usePasswordStrength();
    const [username, setUsername] = useState(user?.username ?? '');
    const [email, setEmail] = useState(user?.email ?? '');
    const [confirmPassword, setConfirmPassword] = useState('');

    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitError, setSubmitError] = useState('');
    const [submitSuccess, setSubmitSuccess] = useState(false);
    const [validationErrors, setValidationErrors] = useState<Record<string, string>>({});

    const schema = z
        .object({
            username: z
                .string()
                .min(3, tokens.register.error.username.minLength)
                .max(20, tokens.register.error.username.maxLength)
                .regex(/^[a-zA-Z0-9_]+$/, tokens.register.error.username.regex),
            email: z.string().email(tokens.register.error.email.invalid),
            password: z.string().min(8, tokens.register.error.password.minLength),
            confirmPassword: z.string(),
        })
        .refine((data) => data.password === data.confirmPassword, {
            message: tokens.register.error.confirmPassword.mismatch,
            path: ['confirmPassword'],
        });

    const formValues = { username, email, password, confirmPassword };

    const isFormValid = (() => {
        const hasAllFields = [username, email, password, confirmPassword].every((v) => v.trim() !== '');

        try {
            schema.parse(formValues);

            return hasAllFields && isPasswordStrong;
        } catch {
            return false;
        }
    })();

    const validateField = (field: string) => {
        try {
            schema.parse(formValues);
            setValidationErrors((prev) => {
                const next = { ...prev };
                delete next[field];

                return next;
            });
        } catch (error) {
            if (error instanceof z.ZodError) {
                const fieldError = error.issues.find((err) => err.path[0] === field);
                if (fieldError) {
                    setValidationErrors((prev) => ({ ...prev, [field]: fieldError.message }));
                }
            }
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        try {
            schema.parse(formValues);
            setValidationErrors({});
        } catch (error) {
            if (error instanceof z.ZodError) {
                const errors: Record<string, string> = {};
                error.issues.forEach((err) => {
                    if (err.path[0]) {
                        errors[err.path[0] as string] = err.message;
                    }
                });
                setValidationErrors(errors);
            }

            return;
        }

        if (!isPasswordStrong) {
            setSubmitError(tokens.register.error.password.notStrong);

            return;
        }

        setIsSubmitting(true);
        setSubmitError('');

        try {
            if (mode === 'update') {
                await api().user().update(username, email, password);
            } else {
                await api().user().register({ username, email, password, passwordConfirmation: confirmPassword });
            }

            setSubmitSuccess(true);
            setUsername('');
            setEmail('');
            setPassword('');
            setConfirmPassword('');
            setValidationErrors({});
        } catch (error) {
            if (error instanceof Error) {
                if (error.message.includes('register.error.')) {
                    try {
                        setSubmitError(JSON.parse(error.message).message || tokens.register.error.miscellaneous);
                    } catch {
                        setSubmitError(tokens.register.error.miscellaneous);
                    }
                } else {
                    setSubmitError(error.message);
                }
            } else {
                setSubmitError(tokens.register.error.miscellaneous);
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="flex min-h-[calc(100vh-4rem)] w-full items-center justify-center bg-slate-50 p-4 dark:bg-slate-950">
            <div className="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg dark:bg-slate-900">
                <h1 className="mb-1 text-center text-3xl font-bold text-slate-900 dark:text-white">
                    {t(tokens[mode].title)}
                </h1>
                <p className="mb-6 text-center text-slate-500 dark:text-slate-400">{t(tokens[mode].subtitle)}</p>

                {submitSuccess && (
                    <div className="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                        {mode === 'update' ? t(tokens.update.success) : t(tokens.register.success)}
                    </div>
                )}

                {submitError && (
                    <div className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 font-medium text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                        {t(submitError)}
                    </div>
                )}

                {!submitSuccess && (
                    <form onSubmit={handleSubmit} className="flex flex-col gap-5">
                        <Field
                            id="username"
                            label={t(tokens.user.username)}
                            value={username}
                            onChange={setUsername}
                            onBlur={() => validateField('username')}
                            error={validationErrors.username && t(validationErrors.username)}
                            placeholder={t(tokens.register.placeholder.username)}
                        />
                        <Field
                            id="email"
                            label={t(tokens.user.email)}
                            value={email}
                            onChange={setEmail}
                            onBlur={() => validateField('email')}
                            error={validationErrors.email && t(validationErrors.email)}
                            placeholder={t(tokens.register.placeholder.email)}
                        />
                        <div className="flex flex-col gap-2">
                            <Field
                                id="password"
                                type="password"
                                label={t(tokens.user.password)}
                                value={password}
                                onChange={setPassword}
                                onBlur={() => validateField('password')}
                                error={validationErrors.password && t(validationErrors.password)}
                                placeholder={t(tokens.register.placeholder.password)}
                            />
                            {password && (
                                <div className="mt-1">
                                    <div className="h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                                        <div
                                            className="h-full rounded-full transition-all"
                                            style={{
                                                width: `${(strengthResult.score / 5) * 100}%`,
                                                backgroundColor: strengthResult.color,
                                            }}
                                        />
                                    </div>
                                    <span className="mt-1 text-sm font-semibold" style={{ color: strengthResult.color }}>
                                        {t(strengthResult.label)}
                                    </span>
                                    {strengthResult.feedbacks.length > 0 && (
                                        <ul className="mt-1 list-disc pl-5 text-xs text-slate-500 dark:text-slate-400">
                                            {strengthResult.feedbacks.map((item) => (
                                                <li key={item}>{t(item)}</li>
                                            ))}
                                        </ul>
                                    )}
                                </div>
                            )}
                        </div>
                        <Field
                            id="confirmPassword"
                            type="password"
                            label={t(tokens.register.confirmPassword)}
                            value={confirmPassword}
                            onChange={setConfirmPassword}
                            onBlur={() => validateField('confirmPassword')}
                            error={validationErrors.confirmPassword && t(validationErrors.confirmPassword)}
                            placeholder={t(tokens.register.placeholder.confirmPassword)}
                        />

                        <button
                            type="submit"
                            disabled={!isFormValid || isSubmitting}
                            className="mt-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 font-semibold text-white transition disabled:cursor-not-allowed disabled:from-slate-400 disabled:to-slate-400"
                        >
                            {isSubmitting ? t(tokens.register.submitting) : t(tokens[mode].submit)}
                        </button>
                    </form>
                )}

                {submitSuccess && !update && (
                    <p className="mt-6 text-center text-slate-500 dark:text-slate-400">
                        <Link href="/login" className="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                            {t(tokens.login.title)}
                        </Link>
                    </p>
                )}
            </div>
        </div>
    );
}

function Field({
    id,
    label,
    value,
    onChange,
    onBlur,
    error,
    placeholder,
    type = 'text',
}: {
    id: string;
    label: string;
    value: string;
    onChange: (v: string) => void;
    onBlur: () => void;
    error?: string | false;
    placeholder: string;
    type?: string;
}) {
    return (
        <div className="flex flex-col gap-1.5">
            <label htmlFor={id} className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                {label} <span className="text-red-500">*</span>
            </label>
            <input
                id={id}
                type={type}
                value={value}
                onChange={(e) => onChange(e.target.value)}
                onBlur={onBlur}
                placeholder={placeholder}
                className={`rounded-lg border-2 px-3 py-2 text-slate-900 outline-none transition focus:border-indigo-500 dark:bg-slate-800 dark:text-white ${
                    error ? 'border-red-400' : 'border-slate-200 dark:border-slate-700'
                }`}
            />
            {error && <span className="text-sm font-medium text-red-500">{error}</span>}
        </div>
    );
}
