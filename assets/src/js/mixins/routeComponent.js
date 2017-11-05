export default {
    data() {
        return {
            isLoaded: false
        };
    },

    beforeRouteEnter(to, from, next) {
        next((vm) => {
            weMail.ajax.get('get_route_data', {
                name: vm.$options.routeName,
                params: to.params,
                query: to.query
            }).done((response) => {
                let _mutations = vm.$options.mutations;

                if (vm.getMutations) {
                    _mutations = vm.getMutations();
                }

                const mutations = $.extend(true, {}, _mutations);

                const getters = $.extend(true, {}, vm.$options.getters);

                // first unregister the module if it already exists
                // Note: I've faced duplicating error only for getters :: Edi Amin
                if (vm.$store.state[vm.$options.routeName]) {
                    vm.$store.unregisterModule(vm.$options.routeName);
                }

                vm.$store.registerModule(vm.$options.routeName, {
                    namespaced: true,
                    state: response.data,
                    mutations,
                    getters
                });

                vm.isLoaded = true;

                if (typeof vm.afterLoaded === 'function') {
                    vm.afterLoaded();
                }
            });
        });
    }
};
