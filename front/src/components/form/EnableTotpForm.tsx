'use client';

import { useState } from 'react';
import { QRCodeSVG } from 'qrcode.react';

import { useLocale } from '@/context/LocaleContext';
import tokens from '@/i18n/tokens';
import api from '@/lib/api/api';
import BasicButton from '@/components/ui/BasicButton';

import type { User } from '@/lib/api/resources/user';

export default function EnableTotpForm({ user }: { user: User }) {
    const { t } = useLocale();
    const [activated, setActivated] = useState(false);
    const [qrCodeUrl, setQrCodeUrl] = useState<string | null>(null);

    const handleActivate = async () => {
        const response = await api().user().activateTotp();
        setActivated(true);
        setQrCodeUrl(response.qrCodeUrl);
    };

    if (user.totp) {
        return null;
    }

    return (
        <div className="flex flex-col items-center gap-4 rounded-2xl bg-white p-8 text-center shadow-lg dark:bg-slate-900">
            {!activated ? (
                <BasicButton text={t(tokens.totp.enable)} onClick={handleActivate} />
            ) : (
                qrCodeUrl && (
                    <>
                        <QRCodeSVG value={qrCodeUrl} size={200} />
                        <p className="text-slate-600 dark:text-slate-300">{t(tokens.totp.description)}</p>
                    </>
                )
            )}
        </div>
    );
}
