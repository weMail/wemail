<template>
    <div v-if="isLoaded">
        <h1>
            Subscribers
            <a href="#" class="page-title-action">Add New</a>
            <a href="#" class="page-title-action">Search Segment</a>
        </h1>

        <div class="row">
            <div class="col-6">
                <p><input type="text" v-model="search"></p>

                <ul>
                    <li v-for="subscriber in subscribers.data">
                        hash: {{ subscriber.hash }} | <router-link :to="{name: 'subscriberShow', params: {hash: subscriber.hash}}">{{ subscriber.first_name }} {{ subscriber.last_name }}</router-link>
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
        routeName: 'subscriberIndex',

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
            ...Vuex.mapState('subscriberIndex', ['i18n', 'subscribers'])
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
                    Vue.delete(query, 's');
                }

                this.$router.replace({
                    query
                });
            }
        }
    };
</script>
