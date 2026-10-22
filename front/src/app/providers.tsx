'use client';

import { AuthProvider } from '@/context/AuthContext';
import { LocaleProvider } from '@/context/LocaleContext';
import Navbar from '@/components/layout/Navbar';
import Footer from '@/components/layout/Footer';

export default function Providers({ children }: { children: React.ReactNode }) {
    return (
        <LocaleProvider>
            <AuthProvider>
                <Navbar />
                <main className="flex-1 pt-16">{children}</main>
                <Footer />
            </AuthProvider>
        </LocaleProvider>
    );
}
