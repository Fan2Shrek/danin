/* eslint-disable-next-line @typescript-eslint/ban-ts-comment */
// @ts-nocheck

import { ref } from 'vue';
import { defineStore } from 'pinia';

import api from '@/lib/api/api';

export const useApiStore = defineStore('store', () => {
    const store = ref({});

    const makeAction = async (action: string, params = []) => {
        if (!storeActions.hasOwnProperty(action)) {
            throw new Error(`Action ${action} does not exist.`);
        }

        await storeActions[action](store.value, params);

        return store.value;
    };

    const getStoreState = async (key: string) => {
        if (!store.value.hasOwnProperty(key)) {
            let params = [];
            let actionName = key;
            if (key.includes('_')) {
                const parts = key.split('_');
                actionName = parts[0];
                params = [parts[1]];
            }

            await makeAction(
                `fetch${actionName.charAt(0).toUpperCase() + actionName.slice(1)}`,
                params,
            );
        }

        return store.value[key];
    };

    return {
        makeAction,
        getStoreState,
    };
});

export const storeActions = {
    fetchGames: async (store) => {
        store.games = await api().game().getAll();
    },
    fetchCommands: async (store, [gameName]) => {
        store[`commands_${gameName}`] = await api().game().getCommands(gameName);
    },
    fetchProviders: async (store) => {
        store.providers = await api().provider().getAll();
    },
};
