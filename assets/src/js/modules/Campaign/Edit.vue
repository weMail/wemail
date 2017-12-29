<template>
    <div v-if="isLoaded">
        <router-view></router-view>
        <progress-bar></progress-bar>
    </div>
</template>

<script>
    import ProgressBar from './ProgressBar.vue';

    export default {
        routeName: 'campaignEdit',

        mutations: {
            updateCampaign(state, payload) {
                state.campaign = payload;
            },

            setEmailTemplate(state, payload) {
                state.campaign.email.template = payload;
            }
        },

        mixins: weMail.getMixins('routeComponent'),

        components: {
            ProgressBar
        },

        computed: {
            ...Vuex.mapState('campaignEdit', ['campaign'])
        },

        methods: {
            registeredStoreModule() {
                if (!this.$store.state.campaignEdit.campaign) {
                    this.$router.push({
                        name: 'campaign404'
                    });

                    return false;
                }

                if (this.campaign.status === 'active' || this.campaign.status === 'completed') {
                    this.$router.push({
                        name: 'campaignShow',
                        params: {
                            id: this.campaign.id
                        }
                    });

                    return false;
                }

                return true;
            }
        }
    };
</script>
