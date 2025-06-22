import Resource from './resource';

export type Provider = {
    id: string;
    commands?: string;
    image?: string;
};

class ProviderResource extends Resource {
    public async getAll(): Promise<Provider[]> {
        const response: { member: Provider[] } = await this.get(`/api/providers`);

        return response.member;
    }
}

export default ProviderResource;
