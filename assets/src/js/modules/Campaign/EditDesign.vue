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
    import settingsWpPosts from './customizer/settings/WpPosts.vue';
    import settingsWpLatestContents from './customizer/settings/WpLatestContents.vue';

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

            weMail.Vue.component('customizer-content-settings-wp-posts', settingsWpPosts);
            weMail.Vue.component('customizer-content-settings-wp-latest-contents', settingsWpLatestContents);

            $('body').addClass('wemail-fixed-body');
        },

        destroyed() {
            $('body').removeClass('wemail-fixed-body');
        }
    };
</script>
