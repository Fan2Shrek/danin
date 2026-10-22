import api from '@/lib/api/api';

type Store = Record<string, unknown>;

const store: Store = {};

const storeActions: Record<string, (params: string[]) => Promise<unknown>> = {
    fetchGames: async () => api().game().getAll(),
    fetchCommands: async ([gameName]) => api().game().getCommands(gameName),
    fetchProviders: async () => api().provider().getAll(),
    fetchArticle: async ([slug]) => api().article().getBySlug(slug),
    fetchTransports: async () => api().game().getTransports(),
    fetchArticlesList: async () => api().article().getList(),
    fetchCurrentRooms: async () => api().room().getCurrents(),
    fetchEvents: async () => api().event().getAll(),
};

const localeKeys = ['games', 'commands', 'article', 'articlesList', 'transports'];

export async function getStoreState<T>(locale: string, key: string): Promise<T> {
    const [prefix, ...rest] = key.split('_');
    const isLocaleScoped = localeKeys.includes(prefix);
    const bucket = isLocaleScoped ? ((store[locale] ??= {}) as Store) : store;

    if (!(key in bucket)) {
        const actionName = `fetch${prefix.charAt(0).toUpperCase()}${prefix.slice(1)}`;
        const action = storeActions[actionName];

        if (!action) {
            throw new Error(`Action ${actionName} does not exist.`);
        }

        bucket[key] = await action(rest);
    }

    return bucket[key] as T;
}
