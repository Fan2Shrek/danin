import Resource from './resource';

export type Event = {
    id: number;
    title: string;
    link: string;
    startAt: string;
};

class EventResource extends Resource {
    public async getAll(): Promise<Event[]> {
        const response: { member: Event[] } = await this.get('/api/events');

        return response.member;
    }
}

export default EventResource;
