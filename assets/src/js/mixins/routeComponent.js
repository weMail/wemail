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

            let endpoint = vm.$options.apiEndpoint;

            const params = endpoint.match(/:[^/]+/g);

            if (params) {
                params.forEach((rawParam) => {
                    const routerParam = vm.$route.params[rawParam.replace(':', '')];

                    endpoint = endpoint.replace(rawParam, routerParam);
                });
            }

            const query = vm.$route.query;

            weMail.api.query(query).get(endpoint).done((response) => {
                // `expects` array contains the data we expects from weMail API
                const expects = vm.$options.expects || ['data'];
                const expectedData = {};

                expects.forEach((prop, index) => {
                    if (index === 0) {
                        expectedData.data = prop;
                    } else {
                        expectedData[prop] = prop;
                    }
                });

                if (!Object.keys(expectedData).every(key => key in response)) { // eslint-disable-line arrow-parens
                    return vm.error(__('API error. Expected data not found.'));
                }

                // make an object with the expected keys as its properties having empty values
                const state = Object.keys(expectedData).reduce((acc, val) => {
                    acc[expectedData[val]] = response[val];
                    return acc;
                }, {});

                let _mutations = vm.$options.mutations;

                if (vm.getMutations) {
                    _mutations = vm.getMutations();
                }

                const mutations = $.extend(true, {}, _mutations);

                const getters = $.extend(true, {}, vm.$options.getters);

                // first unregister the module if it already exists
                // Note: I've faced duplicating error only for getters :: Edi Amin
                if (vm.$store.state[vm.$options.name]) {
                    vm.$store.unregisterModule(vm.$options.name);
                }

                vm.$store.registerModule(vm.$options.name, {
                    namespaced: true,
                    state,
                    mutations,
                    getters
                });

                // Trigger registeredStoreModule.
                if (typeof vm.registeredStoreModule === 'function') {
                    const shouldContinue = vm.registeredStoreModule();

                    if (!shouldContinue) {
                        return false;
                    }
                }

                // we have our state data at this point, so render the dom
                vm.isLoaded = true;

                // We may need to bind some plugin to new rendered dom
                // so, we'll wait to finish the render, and do our things
                // after vue triggered the nextTick method
                vm.callAfterLoaded();

                return true;
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
