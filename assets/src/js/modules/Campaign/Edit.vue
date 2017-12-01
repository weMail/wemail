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
        }
    };
</script>
