<template>
    <div v-if="isLoaded">
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

        computed: {
            ...weMail.Vuex.mapState('campaignEdit', ['i18n'])
        }
    };
</script>

<style lang="scss">
</style>
