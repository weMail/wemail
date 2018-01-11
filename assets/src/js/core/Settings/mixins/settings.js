export default {
    data() {
        return {
            isLoaded: false
        };
    },

    computed: {
        i18n() {
            return this.$store.state[this.$options.name] ? this.$store.state[this.$options.name].i18n : {};
        },

        settings() {
            return this.$store.state[this.$options.name] ? this.$store.state[this.$options.name].settings : {};
        }
    },

    created() {
        const vm = this;

        weMail.ajax.get('get_route_data', {
            name: vm.$options.name
        }).done((response) => {
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
    },

    methods: {
        callAfterLoaded() {
            const vm = this;

            Vue.nextTick(() => {
                if (typeof vm.afterLoaded === 'function') {
                    vm.afterLoaded();
                }

                vm.$emit('loaded', vm);
            });
        }
    }
};
