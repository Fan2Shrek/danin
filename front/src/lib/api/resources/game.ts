import Resource from './resource';

export type Game = {
    id: string;
    name: string;
    description: string;
    image?: string;
    setupArticleSlug?: string;
};

export type Command = {
    id: string;
    name: string;
    description: string;
};

export type Transport = {
    id: string;
    name: string;
    fields: string[];
};

class GameResource extends Resource {
    public async getAll(): Promise<Game[]> {
        const response: { member: Game[] } = await this.get(`/api/games`);

        return response.member;
    }

    public async getCommands(gameName: string): Promise<Command[]> {
        const response: { member: Command[] } = await this.get(`/api/games/${gameName}/commands`);

        return response.member;
    }

    public async getTransports(): Promise<Transport[]> {
        const response: { member: Transport[] } = await this.get(`/api/transports`);

        return response.member;
    }
}

export default GameResource;
