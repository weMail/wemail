export default {
    beforeCreate() {
        const mutations = $.extend(true, {}, this.$options.mutations);

        this.$store.registerModule(this.$options.routeName, {
            namespaced: true,
            state: weMail.stores[this.$options.routeName].state,
            mutations
        });
    }
};
