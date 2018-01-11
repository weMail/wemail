<template>
    <customizer
        context="campaign"
        :customizer="customizer"
        :template="emailTemplate"
        :is-preview="isPreview"
    >
        <div slot="header">
            <h1 class="edit-template-title">{{ pageTitle }}</h1>

            <div v-if="isPreview" class="preview-header clearfix">
                <strong class="preview-template-name">{{ __('Template') }}: {{ templateName }}</strong>

                <span class="float-right">
                    <button
                        type="button"
                        class="button button-primary"
                        @click="selectTemplate"
                    >{{ __('Select Template') }}</button>

                    <button
                        type="button"
                        class="button"
                        @click="closePreview"
                    >{{ __('Close') }}</button>
                </span>
            </div>
        </div>
    </customizer>
</template>

<script>
    import Customizer from 'js/core/Customizer/Customizer.vue';
    import contentWpPosts from './customizer/content/WpPosts.vue';
    import contentWpLatestContents from './customizer/content/WpLatestContents.vue';
    import settingsWpPosts from './customizer/settings/WpPosts.vue';
    import settingsWpLatestContents from './customizer/settings/WpLatestContents.vue';

    export default {
        components: {
            Customizer
        },

        props: {
            isPreview: {
                type: Boolean,
                required: false,
                default: false
            },

            template: {
                type: Object,
                required: false,
                default() {
                    return {};
                }
            },

            title: {
                type: String,
                required: false,
                default: ''
            },

            templateName: {
                type: String,
                required: false,
                default: ''
            }
        },

        computed: {
            ...Vuex.mapState('campaignEdit', ['customizer']),

            emailTemplate() {
                if (!this.isPreview) {
                    return this.$store.state.campaignEdit.campaign.email.template;
                }

                return this.template;
            },

            pageTitle() {
                if (!this.title) {
                    return __('Edit Campaign');
                }

                return this.title;
            }
        },

        created() {
            weMail.setCustomizerContentComponents('campaign', {
                contentWpPosts,
                contentWpLatestContents
            });

            Vue.component('customizer-content-settings-wp-posts', settingsWpPosts);
            Vue.component('customizer-content-settings-wp-latest-contents', settingsWpLatestContents);

            $('body').addClass('wemail-fixed-body');
        },

        destroyed() {
            $('body').removeClass('wemail-fixed-body');
        },

        methods: {
            selectTemplate() {
                weMail.event.$emit('campaign-customizer-select-template');
            },

            closePreview() {
                weMail.event.$emit('campaign-customizer-close-preview');
            }
        }
    };
</script>

<style lang="scss">
    .edit-template-title {
        margin-bottom: 8px !important;
    }

    .preview-header {
        padding: 10px;
        background-color: #fff;
        border: 1px solid $wp-border-color;
        border-bottom: 0;

        .preview-template-name {
            font-size: 20px;
            font-weight: 300;
            line-height: 1.4;
        }
    }
</style>
