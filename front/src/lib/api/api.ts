import Client from './client';
import UserResource from './resources/user';

class Api {
    private client: Client;

    private userResource: UserResource;

    constructor() {
        this.client = new Client(import.meta.env.VITE_API_URL);

        this.userResource = new UserResource(this.client);
    }

    public user(): UserResource {
        return this.userResource;
    }
}

const instance = new Api();
const api = () => instance;

export default api;
