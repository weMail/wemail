<template>
    <div v-if="isLoaded">
        <h1>
            {{ i18n.lists }}
            <a href="#" class="page-title-action">Add New</a>
        </h1>

        <div class="row">
            <div class="col-6">
                <p><input type="text" v-model="search"></p>

                <ul>
                    <li v-for="list in lists.data">
                        id: {{ list.id }} | <router-link :to="{name: 'listsShow', params: {id: list.id}}">{{ list.name }}</router-link>
                    </li>
                </ul>

            </div>
            <div class="col-6">
                <pre>{{ lists }}</pre>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'listsIndex',

        mutations: {
            updateLists(state, payload) {
                state.lists = payload;
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
            ...Vuex.mapState('listsIndex', ['i18n', 'lists'])
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
                            vm.$store.commit('listsIndex/updateLists', response);
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
