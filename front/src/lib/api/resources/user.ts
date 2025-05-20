import Resource from './resource';

type Response = {
    token: string;
    refresh_token: string;
};

class UserResource extends Resource {
    public async login(email: string, password: string): Promise<Response> {
        return await this.post(`/api/login`, {
            email,
            password,
        });
    }

    public async refreshToken(refreshToken: string): Promise<Response> {
        return await this.post(`/api/token/refresh`, {
            refresh_token: refreshToken,
        });
    }
}

export default UserResource;
