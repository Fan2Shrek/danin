import Client from './client';
import UserResource from './resources/user';
import TchatResource from './resources/tchat';
import GameResource from './resources/game';
import MercureResource from './resources/mercure';
import RoomResource from './resources/room';
import ProviderResource from './resources/provider';
import ArticleResource from './resources/article';
import { deleteCookie, getCookie, setCookie } from '../cookies';
import EventResource from './resources/event';

class Api {
    private client: Client;
    private refreshToken: string | null = null;

    private isRefreshing: boolean = false;

    private userResource: UserResource;
    private tchatResource: TchatResource;
    private gameResource: GameResource;
    private mercureResource: MercureResource;
    private roomResource: RoomResource;
    private providerResource: ProviderResource;
    private articleResource: ArticleResource;
    private eventResource: EventResource;

    constructor() {
        this.client = new Client(import.meta.env.VITE_API_URL);
        this.client.setToken(getCookie('token') || '');
        this.refreshToken = getCookie('refresh_token');

        this.userResource = new UserResource(this.client);
        this.tchatResource = new TchatResource(this.client);
        this.gameResource = new GameResource(this.client);
        this.mercureResource = new MercureResource(this.client);
        this.roomResource = new RoomResource(this.client);
        this.providerResource = new ProviderResource(this.client);
        this.articleResource = new ArticleResource(this.client);
        this.eventResource = new EventResource(this.client);

        this.client.setRefreshToken(async () => {
            if (!this.isRefreshing && this.refreshToken) {
                deleteCookie('token');
                this.setToken('');
                this.isRefreshing = true;

                try {
                    const response = await this.userResource.refreshToken(this.refreshToken);

                    this.setToken(response.token);
                    setCookie('token', response.token);
                } finally {
                    this.isRefreshing = false;
                }
            }
        });
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

    public room(): RoomResource {
        return this.roomResource;
    }

    public provider(): ProviderResource {
        return this.providerResource;
    }

    public article(): ArticleResource {
        return this.articleResource;
    }

    public event(): EventResource {
        return this.eventResource;
    }

    public image(path: string): string {
        if (path.startsWith('http')) {
            return path;
        }

        return `${this.client.getUrl()}/${path}`;
    }

    public setToken(token: string): void {
        this.client.setToken(token);
    }

    public setRefreshToken(refreshToken: string): void {
        this.client.setToken(refreshToken);
    }

    public setLocale(locale: string): void {
        this.client.setLocale(locale);
    }

    public async refresh(): Promise<void> {
        return await this.client.refresh();
    }
}

const instance = new Api();
const api = () => instance;

export default api;
