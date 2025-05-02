import Resource from './resource';

export type Message = {
    '@id': number;
    author: string;
    content: string;
    sendAt: string;
};

class TchatResource extends Resource {
    public async sendMessage(message: string, roomId: number): Promise<void> {
        return await this.post(`/api/rooms/${roomId}/messages`, {
            content: message,
        });
    }

    public async getMessages(roomId: number): Promise<Message[]> {
        const response: { member: Message[] } = await this.get(`/api/rooms/${roomId}/messages`);

        return response.member;
    }
}

export default TchatResource;
