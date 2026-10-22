'use client';

import { createContext, useCallback, useContext, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

import api from '@/lib/api/api';
import { deleteCookie, getCookie, setCookie } from '@/lib/cookies';

import type { LoginResponse, RegistrationData, Response, User, UserResponse } from '@/lib/api/resources/user';

type AuthContextValue = {
    user: User | null;
    authChecked: boolean;
    loginUser: (email: string, password: string) => Promise<LoginResponse>;
    registerUser: (user: RegistrationData) => Promise<UserResponse>;
    verifyCode: (code: string) => Promise<void>;
    logoutUser: () => void;
    refreshUser: () => Promise<void>;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const [user, setUser] = useState<User | null>(null);
    const [authChecked, setAuthChecked] = useState(false);

    const logoutUser = useCallback(() => {
        setUser(null);
        deleteCookie('token');
        deleteCookie('refresh_token');
    }, []);

    const refreshUser = useCallback(async () => {
        try {
            setUser(await api().user().me());
        } catch {
            logoutUser();
        }
    }, [logoutUser]);

    useEffect(() => {
        (async () => {
            const refreshToken = getCookie('refresh_token');
            const token = getCookie('token');

            if (refreshToken && !token) {
                await api().refresh();
                await refreshUser();
            } else if (token) {
                api().setToken(token);
                await refreshUser();
            }

            setAuthChecked(true);
        })();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const onLoginSuccess = useCallback(
        async (token: string, refreshToken: string | null = null) => {
            api().setToken(token);
            const decoded = atob(token.split('.')[1]);
            setCookie('token', token, new Date(JSON.parse(decoded).exp * 1000));

            if (refreshToken) {
                const date = new Date();
                date.setDate(date.getDate() + 30);
                setCookie('refresh_token', refreshToken, date);
            }

            setUser(await api().user().me());
            router.push('/');
        },
        [router],
    );

    const loginUser = useCallback(
        async (email: string, password: string): Promise<LoginResponse> => {
            const response = await api().user().login(email, password);

            if (response.need_totp) {
                api().setToken(response.token);

                return response;
            }

            await onLoginSuccess(response.token, response.refresh_token);

            return response;
        },
        [onLoginSuccess],
    );

    const registerUser = useCallback(async (data: RegistrationData): Promise<UserResponse> => {
        return await api().user().register(data);
    }, []);

    const verifyCode = useCallback(
        async (code: string) => {
            const response: Response = await api().user().verifyCode(code);
            await onLoginSuccess(response.token);
        },
        [onLoginSuccess],
    );

    return (
        <AuthContext.Provider
            value={{ user, authChecked, loginUser, registerUser, verifyCode, logoutUser, refreshUser }}
        >
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);

    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }

    return context;
}
