export default {
    data() {
        return {
            isFetchingData: true
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

                vm.$store.registerModule(vm.$options.routeName, {
                    namespaced: true,
                    state: response.data,
                    mutations
                });

                vm.isFetchingData = false;

                if (vm.hasOwnProperty('afterFetchingInitialData')) {
                    vm.afterFetchingInitialData();
                }
            });
        });
    }
};
