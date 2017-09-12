<template>
    <div v-if="!isLoaded">
        <h1>
            {{ i18n.campaigns }}
            <router-link :to="{name: 'campaignCreate'}" class="page-title-action">Add New</router-link>
        </h1>

        <div class="row">
            <div class="col-6">
                <p><input type="text" v-model="search"></p>

                <ul>
                    <li v-for="campaign in campaigns.data">
                        id: {{ campaign.id }} | <router-link :to="{name: 'campaignShow', params: {id: campaign.id}}">{{ campaign.name }}</router-link>
                    </li>
                </ul>

            </div>
            <div class="col-6">
                <pre>{{ campaigns }}</pre>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'campaignIndex',

        mutations: {
            updateCampaigns(state, payload) {
                state.campaigns = payload;
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
            ...weMail.Vuex.mapState('campaignIndex', ['i18n', 'campaigns'])
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
                            vm.$store.commit('campaignIndex/updateCampaigns', response);
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
