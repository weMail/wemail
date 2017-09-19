<template>
    <customizer
        context="campaign"
        :iframe-title="iframeTitle"
        :template="campaign.email.template"
        :customizer="customizer">
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

        data() {
            return {
                iframeTitle: 'Email Customizer'
            };
        },

        created() {
            weMail.registerCustomizerComponents('campaign', {
                contentWpPosts,
                contentWpLatestContents
            });
        }
    };
</script>
