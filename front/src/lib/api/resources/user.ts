import Resource from './resource';

type LoginResponse = {
    token: string;
};

class UserResource extends Resource {
    public async login(email: string, password: string): Promise<LoginResponse> {
        return await this.post(`/api/login`, {
            email,
            password,
        });
    }
}

export default UserResource;
