'use client';

import { useAuth } from '@/context/AuthContext';
import UserForm from '@/components/form/UserForm';
import EnableTotpForm from '@/components/form/EnableTotpForm';

export default function ProfilePage() {
    const { user } = useAuth();

    if (!user) {
        return null;
    }

    return (
        <div className="flex flex-col items-center gap-6 py-10">
            <UserForm user={user} update />
            <EnableTotpForm user={user} />
        </div>
    );
}
