export default {
    data() {
        return {
            isLoaded: false
        };
    },

    beforeRouteEnter(to, from, next) {
        next((vm) => {
            if (to.name !== 'authSite' && !weMail.user.hash) {
                vm.$router.push({
                    name: 'authSite'
                });

                return;
            }

            if (vm.$options.permission && !weMail.user.permissions[vm.$options.permission]) {
                vm.$router.push({
                    path: '/404'
                });

                return;
            }

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

                // we have our state data at this point, so render the dom
                vm.isLoaded = true;

                // We may need to bind some plugin to new rendered dom
                // so, we'll wait to finish the render, and do our things
                // after vue triggered the nextTick method
                vm.callAfterLoaded();
            });
        });
    },

    methods: {
        callAfterLoaded() {
            const vm = this;

            if (typeof vm.afterLoaded === 'function') {
                Vue.nextTick(() => {
                    vm.afterLoaded();
                });
            }
        }
    }
};
