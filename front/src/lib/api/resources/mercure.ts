import Resource from './resource';

export default class MercureResource extends Resource {
    public async getToken(topics: string[]): Promise<void> {
        return await this.post(
            `/api/mercure`,
            {
                topics,
            },
            'include',
        );
    }
}
