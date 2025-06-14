import Resource from './resource';

import type { Command } from './game';

type RoomResponse = {
    id: string;
};

export type StartResponse = {
    local_setup?: boolean;
    data: Record<string, string>;
};

export type RoomConfig = {
    game: string;
    transport: string;
    commands: string[];
    config: Record<string, string>;
};

class RoomResource extends Resource {
    public async create(config: RoomConfig): Promise<RoomResponse> {
        const response: RoomResponse = await this.post(`/api/rooms/create`, config);

        return response;
    }

    public async start(room: string): Promise<StartResponse> {
        return await this.post(`/api/rooms/${room}/start`, {});
    }

    public async getCommands(room: string): Promise<Command[]> {
        const response: { member: Command[] } = await this.get(`/api/rooms/${room}/commands`);

        return response.member;
    }
}

export default RoomResource;
