import api from '@/lib/api/api';

class Mercure {
    private baseUrl: string;

    public constructor() {
        this.baseUrl = process.env.NEXT_PUBLIC_MERCURE_URL ?? '';
    }

    public async subscribe(topic: string, onMessage: (data: object) => void): Promise<EventSource> {
        await api().mercure().getToken([topic]);
        const eventSource = new EventSource(`${this.baseUrl}?topic=${encodeURIComponent(topic)}`, {
            withCredentials: true,
        });

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            onMessage(data);
        };

        return eventSource;
    }
}

const mercure = new Mercure();

export default mercure;
