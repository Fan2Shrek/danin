class GameClient {
    private host: string = '127.0.0.1';
    private port: number = 11664;

    public async start(data: LocalData): Promise<{ status: string }> {
        const { 'mercure-url': mercureUrl, 'mercure-token': mercureToken } = data;

        return await fetch(`http://${this.host}:${this.port}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'sec-fetch-mode': 'cors',
            },
            body: JSON.stringify({ mercureUrl, mercureToken }),
        });
    }
}

const gameClient = new GameClient();

export default () => gameClient;
