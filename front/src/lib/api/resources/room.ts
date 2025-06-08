import Resource from './resource';

type RoomResponse = {
    id: string;
};

export type StartResponse = {
    local_setup?: boolean;
    data: Record<string, any>;
};

export type RoomConfig = {
    game: string;
    game: string;
    commands: string[];
    config: Record<string, any>;
};

class RoomResource extends Resource {
    public async create(config: RoomResponse): Promise<RoomResponse> {
        const response: RoomResponse = await this.post(`/api/rooms/create`, config);

        return response;
    }

    public async start(room: string): Promise<StartResponse> {
        return await this.post(`/api/rooms/${room}/start`, {});
    }
}

export default RoomResource;
