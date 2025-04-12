import Client from "../client";

class Resource {
    protected client: Client;

    constructor(client: Client) {
        this.client = client;
    }

    protected async get<T>(url: string): Promise<T> {
        return await this.client.get<T>(url);
    }

    protected async post<T>(url: string, body: any): Promise<T> {
        return await this.client.post<T>(url, body);
    }
}

export default Resource;
