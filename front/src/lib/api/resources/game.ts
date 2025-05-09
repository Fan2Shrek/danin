import Resource from './resource';

export type Game = {
    id: string;
    name: string;
};

export type Command = {
    id: string;
    description: string;
};

class GameResource extends Resource {
    public async getAll(): Promise<Game[]> {
        return await this.get(`/api/games`);
    }

    public async getCommands(gameName: string): Promise<Command[]> {
        const response: { member: Command[] } = await this.get(`/api/games/${gameName}/commands`);

        return response.member;
    }
}

export default GameResource;
