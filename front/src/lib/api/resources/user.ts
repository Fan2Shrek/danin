import Resource from './resource';

export interface Response {
    token: string;
    refresh_token: string;
}

export interface LoginResponse extends Response {
    need_totp?: boolean;
}

export interface UserResponse {
    username: string;
}

export interface RegistrationData {
    username: string;
    email: string;
    password: string;
    passwordConfirmation: string;
}

export interface TotpResponse {
    qrCodeUrl: string;
}

export type User = {
    username: string;
    email: string;
    totp: boolean;
};

class UserResource extends Resource {
    public async login(email: string, password: string): Promise<LoginResponse> {
        return await this.post(`/api/login`, {
            email,
            password,
        });
    }

    public async register(user: RegistrationData): Promise<UserResponse> {
        return await this.post(`/api/register`, {
            username: user.username,
            email: user.email,
            password: user.password,
        });
    }

    public async update(username = null, email = null, password = null): Promise<UserResponse> {
        return await this.post(`/api/me/update`, {
            username: username,
            email: email,
            password: password,
        });
    }

    public async activateTotp(): Promise<TotpResponse> {
        const response: { member: string[] } = await this.post(`/api/activate-totp`, {});

        return {
            qrCodeUrl: response.member[0],
        };
    }

    public async me(): Promise<User> {
        return await this.get(`/api/me`);
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
