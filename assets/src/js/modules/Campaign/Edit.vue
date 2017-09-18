<template>
    <div v-if="!isLoaded">
        <h1>
            {{ pageTitle }}
        </h1>

        <router-view></router-view>

        <progress-bar :i18n="i18n"></progress-bar>
    </div>
</template>

<script>
    import ProgressBar from './templates/ProgressBar.vue';

    export default {
        routeName: 'campaignEdit',

        mixins: weMail.getMixins('routeComponent'),

        components: {
            ProgressBar
        },

        data() {
            return {
                name: '',
                type: 'standard',
                selectedLists: [],
                selectedSegments: []
            };
        },

        computed: {
            ...weMail.Vuex.mapState('campaignEdit', ['i18n']),

            pageTitle() {
                return this.i18n.editCampaign;
            }
        }
    };
</script>

<style lang="scss">
</style>
