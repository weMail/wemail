<template>
    <div>
        <h1>{{ i18n.editCampaign }}</h1>
        <setup namespace="campaignEdit"></setup>
    </div>
</template>

<script>
    import Setup from './templates/Setup.vue';

    export default {
        namespace: 'campaignEdit',

        components: {
            Setup
        },

        computed: {
            ...weMail.Vuex.mapState('campaignEdit', ['i18n'])
        }
    };
</script>
