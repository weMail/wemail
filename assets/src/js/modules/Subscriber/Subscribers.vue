<template>
    <div v-if="!isFetchingData">
        <h2>
            Subscribers
            <a href="#" class="add-new-h2">Add New</a>
            <a href="#" class="add-new-h2">Search Segment</a>
        </h2>

        <div class="row">
            <div class="col-6">
                <p><input type="text" v-model="search"></p>

                <ul>
                    <li v-for="subscriber in subscribers.data">
                        id: {{ subscriber.id }} | <router-link :to="{name: 'subscriber', params: {id: subscriber.id}}">{{ subscriber.first_name }} {{ subscriber.last_name }}</router-link>
                    </li>
                </ul>

            </div>
            <div class="col-6">
                <pre>{{ subscribers }}</pre>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'subscribers',

        mutations: {
            updateSubscribers(state, payload) {
                state.subscribers = payload;
            }
        },

        mixins: weMail.getMixins('routeComponent'),

        data() {
            return {
                search: '',
                apiHandler: {
                    abort() {
                        //
                    }
                }
            };
        },

        computed: {
            ...weMail.Vuex.mapState('subscribers', ['i18n', 'subscribers'])
        },

        beforeMount() {
            this.search = this.$router.currentRoute.query.s;
        },

        watch: {
            '$route.query': 'onChangeRouteQuery',
            search: 'onChangeSearch'
        },

        methods: {
            onChangeRouteQuery() {
                const vm = this;

                vm.apiHandler.abort();

                vm.apiHandler = weMail
                    .api
                    .get(vm.$router.currentRoute.fullPath)
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('subscribers/updateSubscribers', response);
                        }
                    });
            },

            onChangeSearch(search) {
                const query = $.extend(true, {}, this.$router.currentRoute.query);

                if (search) {
                    query.s = search;
                } else {
                    weMail.Vue.delete(query, 's');
                }

                this.$router.replace({
                    query
                });
            }
        }
    };
</script>
