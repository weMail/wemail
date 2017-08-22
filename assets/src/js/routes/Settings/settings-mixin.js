export default {
    computed: {
        i18n() {
            return this.$store.state.i18n;
        },

        settings: {
            get() {
                return this.$store.state.settings;
            },

            set(value) {
                this.$store.commit('updateSettings', value);
            }
        }
    }
};
