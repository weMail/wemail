import CompanyDetails from './company-details.vue';
import SocialNetworks from './social-networks.vue';
import Settings from './Settings.vue';

weMail.component('CompanyDetails', CompanyDetails);
weMail.component('SocialNetworks', SocialNetworks);

// Register store for Overview route
weMail.registerStore('settings', {
    mutations: {
        updateCountryStates(state, payload) {
            state.states = payload;
        },

        updateSettings(state, payload) {
            state.settings = payload;
        }
    }
});

weMail.component('Settings', {
    store: new weMail.Vuex.Store(weMail.stores.settings),
    ...Settings
});
