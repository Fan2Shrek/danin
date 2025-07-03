import { computed, ref } from 'vue';
import tokens from '@/i18n/tokens';

export interface PasswordStrengthResult {
    score: number;
    feedbacks: string[];
    color: string;
    label: string;
}

export function usePasswordStrength() {
    const password = ref('');

    const strengthResult = computed<PasswordStrengthResult>(() => {
        const pwd = password.value;
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

        // Length
        if (pwd.length >= 8) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.minLength);
        }

        // Uppercase
        if (/[A-Z]/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.uppercase);
        }

        // Lowercase
        if (/[a-z]/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.lowercase);
        }

        // Number
        if (/\d/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.number);
        }

        // Special character
        if (/[!@#$%^&*(),.?":{}|<>]/.test(pwd)) {
            score += 1;
        } else {
            feedbacks.push(tokens.user.constraints.password.special);
        }

        // Determine color and label based on score
        let color: string;
        let label: string;

        if (score <= 1) {
            color = '#ef4444'; // red
            label = tokens.user.constraints.password.strength.veryWeak;
        } else if (score <= 2) {
            color = '#f97316'; // orange
            label = tokens.user.constraints.password.strength.weak;
        } else if (score <= 3) {
            color = '#eab308'; // yellow
            label = tokens.user.constraints.password.strength.medium;
        } else if (score <= 4) {
            color = '#22c55e'; // green
            label = tokens.user.constraints.password.strength.strong;
        } else {
            color = '#16a34a'; // dark green
            label = tokens.user.constraints.password.strength.excellent;
        }

        return {
            score,
            feedbacks,
            color,
            label,
        };
    });

    const isPasswordStrong = computed(() => strengthResult.value.score >= 3);

    return {
        password,
        strengthResult,
        isPasswordStrong,
    };
}
