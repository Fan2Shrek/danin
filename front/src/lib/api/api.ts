import Client from './client';
import UserResource from './resources/user';
import TchatResource from './resources/tchat';
import { getCookie } from '../cookies';

class Api {
    private client: Client;

    private userResource: UserResource;
    private tchatResource: TchatResource;

    constructor() {
        this.client = new Client(import.meta.env.VITE_API_URL);
        this.client.setToken(getCookie('token') || '');

        this.userResource = new UserResource(this.client);
        this.tchatResource = new TchatResource(this.client);
    }

    public user(): UserResource {
        return this.userResource;
    }

    public tchat(): TchatResource {
        return this.tchatResource;
    }

    public setToken(token: string): void {
        this.client.setToken(token);
    }
}

const instance = new Api();
const api = () => instance;

export default api;
