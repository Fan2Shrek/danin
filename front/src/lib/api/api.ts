import Client from './client';
import UserResource from './resources/user';
import TchatResource from './resources/tchat';
import GameResource from './resources/game';
import MercureResource from './resources/mercure';
import { getCookie } from '../cookies';

class Api {
    private client: Client;

    private userResource: UserResource;
    private tchatResource: TchatResource;
    private gameResource: GameResource;
    private mercureResource: MercureResource;

    constructor() {
        this.client = new Client(import.meta.env.VITE_API_URL);
        this.client.setToken(getCookie('token') || '');

        this.userResource = new UserResource(this.client);
        this.tchatResource = new TchatResource(this.client);
        this.gameResource = new GameResource(this.client);
        this.mercureResource = new MercureResource(this.client);
    }

    public user(): UserResource {
        return this.userResource;
    }

    public tchat(): TchatResource {
        return this.tchatResource;
    }

    public game(): GameResource {
        return this.gameResource;
    }

    public mercure(): MercureResource {
        return this.mercureResource;
    }

    public setToken(token: string): void {
        this.client.setToken(token);
    }

    public setLocale(locale: string): void {
        this.client.setLocale(locale);
    }
}

const instance = new Api();
const api = () => instance;

export default api;
