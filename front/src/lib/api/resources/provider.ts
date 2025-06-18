import Resource from './resource';

export type Provider = {
    name: string;
};

class ProviderResource extends Resource {
    public async getAll(): Promise<Provider[]> {
        const response: { member: Provider[] } = await this.get(`/api/providers`);

        return response.member;
    }
}

export default ProviderResource;
