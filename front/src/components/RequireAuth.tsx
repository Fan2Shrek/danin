'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

import { getCookie } from '@/lib/cookies';

export default function RequireAuth({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    // Cookies aren't readable during SSR, so this must stay `false` through the first
    // client render too (matching the server) and only flip after hydration.
    const [hasAuth, setHasAuth] = useState(false);

    useEffect(() => {
        if (getCookie('token') || getCookie('refresh_token')) {
            // eslint-disable-next-line react-hooks/set-state-in-effect
            setHasAuth(true);
        } else {
            router.replace('/login');
        }
    }, [router]);

    if (!hasAuth) {
        return null;
    }

    return <>{children}</>;
}
