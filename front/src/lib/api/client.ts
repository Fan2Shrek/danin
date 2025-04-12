class Client {
    private baseUrl: string;

    public constructor(baseUrl: string = '') {
        this.baseUrl = baseUrl;
    }

    public async get<T>(url: string): Promise<T> {
        return await this.request<T>('GET', url);
    }

    public async post<T>(url: string, body: object | undefined): Promise<T> {
        return await this.request<T>('POST', url, body, {
            'Content-Type': 'application/json',
        });
    }

    private async request<T>(
        method: string,
        url: string,
        body?: object,
        headers?: HeadersInit,
    ): Promise<T> {
        const response = await fetch(this.baseUrl + url, {
            method,
            headers: headers ? headers : {},
            body: body ? JSON.stringify(body) : undefined,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    }
}

export default Client;
