import api from '@/lib/api/api';

class Mercure {
    private baseUrl: string;

    public constructor() {
        this.baseUrl = import.meta.env.VITE_MERCURE_URL;
    }

    public async subscribe(topic: string, onMessage: (data: object) => void): Promise<void> {
        api().mercure().getToken([topic]);
        const eventSource = new EventSource(`${this.baseUrl}?topic=${encodeURIComponent(topic)}`, {
            withCredentials: true,
        });

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            onMessage(data);
        };
    }
}

export default new Mercure();
