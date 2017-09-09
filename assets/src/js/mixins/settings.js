export default {
    computed: {
        i18n() {
            return this.$store.state[this.$options.routeName].i18n;
        },

        settings() {
            return this.$store.state[this.$options.routeName] ? this.$store.state[this.$options.routeName].settings : {};
        }
    }
};
