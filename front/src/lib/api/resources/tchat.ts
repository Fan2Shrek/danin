import Resource from './resource';

class TchatResource extends Resource {
    // @todo add roomid
    public async sendMessage(message: string): Promise<void> {
        return await this.post(`/api/messages`, {
            content: message,
        });
    }
}

export default TchatResource;
