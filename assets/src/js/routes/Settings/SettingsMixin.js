export default {
    _mutations: {
        updateSettings(state, payload) {
            state.settings = payload;
            weMail.set(state, 'settings', payload);
        }
    },

    beforeCreate() {
        const mutations = $.extend(true, this.$options._mutations, this.$options.mutations);

        this.$store.registerModule(this.$options.routeName, {
            namespaced: true,
            state: weMail.stores[this.$options.routeName].state,
            mutations
        });
    },

    computed: {
        i18n() {
            return this.$store.state[this.$options.routeName].i18n;
        },

        settings: {
            get() {
                return this.$store.state[this.$options.routeName].settings;
            },

            set(value) {
                this.$store.commit(`${this.$options.routeName}/updateSettings`, value);
            }
        }
    }
};
