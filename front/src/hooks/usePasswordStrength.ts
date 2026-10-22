import { useMemo, useState } from 'react';
import tokens from '@/i18n/tokens';

export interface PasswordStrengthResult {
    score: number;
    feedbacks: string[];
    color: string;
    label: string;
}

export function usePasswordStrength() {
    const [password, setPassword] = useState('');

    const strengthResult = useMemo<PasswordStrengthResult>(() => {
        const pwd = password;
        const feedbacks: string[] = [];
        let score = 0;

        if (pwd.length === 0) {
            return {
                score: 0,
                feedbacks: [],
                color: '#e5e7eb',
                label: '',
            };
        }

        if (pwd.length >= 8) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.minLength);
        }

        if (/[A-Z]/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.uppercase);
        }

        if (/[a-z]/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.lowercase);
        }

        if (/\d/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.number);
        }

        if (/[!@#$%^&*(),.?":{}|<>]/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.special);
        }

        let color: string;
        let label: string;

        if (score <= 1) {
            color = '#ef4444';
            label = tokens.user.constraints.password.strength.veryWeak;
        } else if (score <= 2) {
            color = '#f97316';
            label = tokens.user.constraints.password.strength.weak;
        } else if (score <= 3) {
            color = '#eab308';
            label = tokens.user.constraints.password.strength.medium;
        } else if (score <= 4) {
            color = '#22c55e';
            label = tokens.user.constraints.password.strength.strong;
        } else {
            color = '#16a34a';
            label = tokens.user.constraints.password.strength.excellent;
        }

        return { score, feedbacks, color, label };
    }, [password]);

    const isPasswordStrong = strengthResult.score >= 3;

    return { password, setPassword, strengthResult, isPasswordStrong };
}
