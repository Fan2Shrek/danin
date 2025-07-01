import Resource from './resource';

export type Article = {
    slug: string;
    title: string;
    content: string;
};

class ArticleResource extends Resource {
    async getBySlug(slug: string): Promise<Article> {
        return await this.get(`/api/articles/${slug}`);
    }

    async getList(): Promise<Article> {
        return await this.get(`/api/articles_list`);
    }
}

export default ArticleResource;
