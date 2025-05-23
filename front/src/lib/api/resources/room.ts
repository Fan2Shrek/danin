import Resource from './resource';

type RoomResponse = {
    id: string;
};

class RoomResource extends Resource {
    public async create(): Promise<RoomResponse> {
        const response: RoomResponse = await this.post(`/api/rooms/create`, {});

        return response;
    }

    public async start(room: string, host: string, port: number): Promise<void> {
        return await this.post(`/api/rooms/${room}/start`, {
            host,
            port,
        });
    }
}

export default RoomResource;
