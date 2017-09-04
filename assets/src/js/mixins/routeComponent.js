export default {
    beforeCreate() {
        this.$store.registerModule(this.$options.routeName, {
            namespaced: true,
            state: weMail.stores[this.$options.routeName].state
        });
    }
};
