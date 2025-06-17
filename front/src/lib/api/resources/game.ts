import Resource from './resource';

export type Game = {
    id: string;
    name: string;
    description: string;
    image?: string;
};

export type Command = {
    id: string;
    name: string;
    description: string;
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
}

export default GameResource;
