import Client from '../client';

class Resource {
    protected client: Client;

    constructor(client: Client) {
        this.client = client;
    }

    protected async get<T>(url: string): Promise<T> {
        return await this.client.get<T>(url);
    }

    protected async post<T>(
        url: string,
        body: object | undefined,
        credentials: RequestCredentials = 'same-origin',
    ): Promise<T> {
        return await this.client.post<T>(url, body, credentials);
    }
}

export default Resource;
