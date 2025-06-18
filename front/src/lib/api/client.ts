class Client {
    private baseUrl: string;
    private token: string | null = null;
    private refreshToken: (() => void) | null = null;
    private locale: string = 'fr';

    public constructor(baseUrl: string = '') {
        this.baseUrl = baseUrl;
    }

    public async get<T>(url: string): Promise<T> {
        return await this.request<T>('GET', url);
    }

    public async post<T>(
        url: string,
        body: object | undefined,
        credentials: RequestCredentials = 'same-origin',
    ): Promise<T> {
        return await this.request<T>(
            'POST',
            url,
            body,
            {
                'Content-Type': 'application/json',
            },
            credentials,
        );
    }

    public getUrl(): string {
        return this.baseUrl;
    }

    public setToken(token: string): void {
        this.token = token;
    }

    public setRefreshToken(refreshToken: () => void): void {
        this.refreshToken = refreshToken;
    }

    public setLocale(locale: string): void {
        this.locale = locale;
    }

    private async request<T>(
        method: string,
        url: string,
        body?: object,
        headers?: HeadersInit,
        credentials: RequestCredentials = 'same-origin',
    ): Promise<T> {
        const response = await fetch(this.baseUrl + url, {
            method,
            headers: this._buildHeaders(headers || null),
            body: body ? JSON.stringify(body) : undefined,
            credentials,
        });

        if (!response.ok) {
            // @todo find better way
            if (response.status === 401 && !url.includes('login') && !url.includes('totp')) {
                await this._refreshToken();

                if (!this.token) {
                    throw new Error('Unauthorized: No token available after refresh');
                }

                return this.request<T>(method, url, body, headers, credentials);
            }

            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    }

    private async _refreshToken(): Promise<void> {
        if (this.refreshToken) {
            await this.refreshToken();
        }
    }

    private _buildHeaders(headers: HeadersInit | null): HeadersInit {
        if (!headers) {
            headers = {};
        }

        const realHeaders = new Headers(headers);

        if (this.token) {
            realHeaders.set('Authorization', `Bearer ${this.token}`);
        }

        if (this.locale) {
            realHeaders.set('Accept-Language', this.locale);
        }

        return realHeaders;
    }
}

export default Client;
