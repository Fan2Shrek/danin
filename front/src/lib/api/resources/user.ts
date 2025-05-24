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

    public async verifyCode(code: string): Promise<Response> {
        return await this.post(`/api/check-totp`, {
            code,
        });
    }

    public async refreshToken(refreshToken: string): Promise<Response> {
        return await this.post(`/api/token/refresh`, {
            refresh_token: refreshToken,
        });
    }
}

export default UserResource;
