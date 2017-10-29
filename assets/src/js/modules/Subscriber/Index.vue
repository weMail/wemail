<template>
    <div v-if="isLoaded">
        <h1>
            Subscribers
            <a
                href="#add-new-subscriber"
                class="page-title-action"
                @click.prevent="showNewSubscriberModal"
            >{{ i18n.addNew }}</a>

            <a
                href="#"
                class="page-title-action"
            >{{ i18n.searchSegment }}</a>
        </h1>

        <div class="row">
            <div class="col-6">
                <p><input type="text" v-model="search"></p>

                <ul>
                    <li v-for="subscriber in subscribers.data">
                        <router-link :to="{name: 'subscriberShow', params: {id: subscriber.id}}">id: {{ subscriber.id }} | {{ subscriber.email }}</router-link>
                    </li>
                </ul>

            </div>
            <div class="col-6">
                <pre>{{ subscribers }}</pre>
            </div>
        </div>

        <new-subscriber-modal
            scope="subscriber-index"
            :i18n="i18n"
            :lists="lists"
        ></new-subscriber-modal>
    </div>
</template>

<script>
    import NewSubscriberModal from './NewSubscriberModal.vue';

    export default {
        routeName: 'subscriberIndex',

        mutations: {
            updateSubscribers(state, payload) {
                state.subscribers = payload;
            }
        },

        mixins: weMail.getMixins('routeComponent'),

        components: {
            NewSubscriberModal
        },

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
            ...Vuex.mapState('subscriberIndex', ['i18n', 'subscribers', 'lists'])
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

                // console.log(vm.$router.currentRoute.fullPath);

                console.log();
                vm.apiHandler = weMail
                    .api
                    .subscribers()
                    .query(vm.$router.currentRoute.query)
                    .get()
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('subscriberIndex/updateSubscribers', response);
                        }
                    }).always((response) => {
                        console.log(response);
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
            },

            showNewSubscriberModal() {
                weMail.event.$emit('show-new-subscriber-modal-subscriber-index');
            }
        }
    };
</script>
