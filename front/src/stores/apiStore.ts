/* eslint-disable-next-line @typescript-eslint/ban-ts-comment */
// @ts-nocheck

import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useI18n } from 'vue-i18n';

import api from '@/lib/api/api';

export const useApiStore = defineStore('store', () => {
    const store = ref({});
    const i18nLocale = useI18n();

    const makeAction = async (action: string, params = []) => {
        if (!storeActions.hasOwnProperty(action)) {
            throw new Error(`Action ${action} does not exist.`);
        }

        await storeActions[action](store.value, params);

        return store.value;
    };

    const getStoreState = async (key: string) => {
        if (!store.value[i18nLocale.locale.value]) {
            store.value[i18nLocale.locale.value] = {};
        }

        if (
            !store.value[i18nLocale.locale.value].hasOwnProperty(key) &&
            !store.value.hasOwnProperty(key)
        ) {
            const params = [i18nLocale.locale.value];
            let actionName = key;
            if (key.includes('_')) {
                const parts = key.split('_');
                actionName = parts[0];
                params.push(parts[1]);
            }

            await makeAction(
                `fetch${actionName.charAt(0).toUpperCase() + actionName.slice(1)}`,
                params,
            );
        }

        return store.value[i18nLocale.locale.value][key] || store.value[key];
    };

    return {
        makeAction,
        getStoreState,
    };
});

export const storeActions = {
    fetchGames: async (store, [locale]) => {
        store[locale].games = await api().game().getAll();
    },
    fetchCommands: async (store, [locale, gameName]) => {
        store[locale][`commands_${gameName}`] = await api().game().getCommands(gameName);
    },
    fetchProviders: async (store) => {
        store.providers = await api().provider().getAll();
    },
    fetchArticle: async (store, [locale, slug]) => {
        store[locale][`article_${slug}`] = await api().article().getBySlug(slug);
    },
};
