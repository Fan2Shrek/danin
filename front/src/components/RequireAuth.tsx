'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

import { getCookie } from '@/lib/cookies';

export default function RequireAuth({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const [hasAuth] = useState(() => Boolean(getCookie('token') || getCookie('refresh_token')));

    useEffect(() => {
        if (!hasAuth) {
            router.replace('/login');
        }
    }, [hasAuth, router]);

    if (!hasAuth) {
        return null;
    }

    return <>{children}</>;
}
