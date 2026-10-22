import Image from 'next/image';

import api from '@/lib/api/api';

import type { Game } from '@/lib/api/resources/game';

type Props = {
    game: Game;
    description?: boolean;
};

export default function GameCard({ game, description = true }: Props) {
    return (
        <article className="flex h-full flex-col items-center rounded-2xl bg-white p-6 text-center shadow-md shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/10 dark:bg-slate-800">
            <span className="mb-2 text-xs font-medium tracking-wide text-slate-400">{game.id}</span>
            {game.image && (
                <Image
                    src={api().image(game.image)}
                    alt={game.name}
                    width={120}
                    height={120}
                    className="mb-4 rounded-xl object-cover"
                    unoptimized
                />
            )}
            <h3 className="mb-2 text-xl font-semibold text-slate-900 dark:text-white">{game.name}</h3>
            {description && <p className="text-slate-600 dark:text-slate-300">{game.description}</p>}
        </article>
    );
}
