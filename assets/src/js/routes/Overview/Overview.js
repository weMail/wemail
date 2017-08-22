import Overview from './Overview.vue';

// Register store for Overview route
weMail.registerStore('overview', {
    mutations: {
        setModelA: (state, payload) => {
            state.modelA = payload;
        },

        setModelB: (state, payload) => {
            state.modelB = payload;
        }
    }
});

weMail.component('Overview', {
    store: new weMail.Vuex.Store(weMail.stores.overview),
    ...Overview
});
