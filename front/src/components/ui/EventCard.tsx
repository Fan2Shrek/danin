import type { Event } from '@/lib/api/resources/event';

export default function EventCard({ event }: { event: Event }) {
    return (
        <a
            href={event.link}
            target="_blank"
            rel="noopener noreferrer"
            className="block rounded-2xl bg-white p-6 text-center shadow-md shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-xl dark:bg-slate-800"
        >
            <h3 className="text-lg font-semibold text-slate-900 dark:text-white">{event.title}</h3>
            {event.startAt && (
                <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {new Date(event.startAt).toLocaleDateString()}
                </p>
            )}
        </a>
    );
}
