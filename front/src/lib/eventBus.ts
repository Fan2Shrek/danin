import { getCurrentInstance } from 'vue';

export const useEmitter = () => {
    const internalInstance = getCurrentInstance();

    return internalInstance?.appContext.config.globalProperties.emitter;
};
