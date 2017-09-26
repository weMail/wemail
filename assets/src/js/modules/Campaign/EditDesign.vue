<template>
    <customizer
        context="campaign"
        :customizer="customizer"
        :template="campaign.email.template"
    >
        <div slot="header">
            <h1>{{ i18n.editCampaign }}</h1>
        </div>
    </customizer>
</template>

<script>
    import Customizer from 'js/modules/Customizer/Customizer.vue';
    import contentWpPosts from './customizer/content/WpPosts.vue';
    import contentWpLatestContents from './customizer/content/WpLatestContents.vue';

    export default {
        components: {
            Customizer
        },

        computed: {
            ...weMail.Vuex.mapState('campaignEdit', ['i18n', 'campaign', 'customizer'])
        },

        created() {
            weMail.setCustomizerContentComponents('campaign', {
                contentWpPosts,
                contentWpLatestContents
            });
        }
    };
</script>
