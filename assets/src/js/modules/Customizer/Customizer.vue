<template>
    <div id="wemail-customizer">
        <slot name="header"></slot>

        <div id="customizer-container" class="d-flex">
            <div id="customizer-stage">
                 <iframe :src="iframeSource" frameborder="0" name="wemail_customizer_iframe" id="wemail-customizer-iframe"></iframe>
            </div>

            <settings
                :context="context"
                :customizer="customizer"
                :template="template"
            ></settings>
        </div>
    </div>
</template>

<script>
    import Iframe from './Iframe.js';
    import Settings from './Settings.vue';
    import contentText from './content/text.vue';
    import contentImage from './content/image.vue';
    import contentImageGroup from './content/imageGroup.vue';
    import contentImageCaption from './content/imageCaption.vue';
    import contentSocialFollow from './content/socialFollow.vue';
    import contentButton from './content/button.vue';
    import contentDivider from './content/divider.vue';
    import contentVideo from './content/video.vue';
    import contentFooter from './content/footer.vue';
    import settingsText from './settings/text.vue';
    import settingsImage from './settings/image.vue';
    import settingsImageGroup from './settings/imageGroup.vue';
    import settingsImageCaption from './settings/imageCaption.vue';
    import settingsSocialFollow from './settings/socialFollow.vue';
    import settingsButton from './settings/button.vue';
    import settingsDivider from './settings/divider.vue';
    import settingsVideo from './settings/video.vue';
    import settingsFooter from './settings/footer.vue';

    export default {
        components: {
            Settings
        },

        props: {
            context: {
                type: String,
                required: true
            },

            customizer: {
                type: Object,
                required: true
            },

            template: {
                type: Object,
                required: true
            }
        },

        computed: {
            iframeSource() {
                return weMail.customizerIframe;
            }
        },

        created() {
            weMail.Vue.component('customizer-content-settings-text', settingsText);
            weMail.Vue.component('customizer-content-settings-image', settingsImage);
            weMail.Vue.component('customizer-content-settings-image-group', settingsImageGroup);
            weMail.Vue.component('customizer-content-settings-image-caption', settingsImageCaption);
            weMail.Vue.component('customizer-content-settings-social-follow', settingsSocialFollow);
            weMail.Vue.component('customizer-content-settings-button', settingsButton);
            weMail.Vue.component('customizer-content-settings-divider', settingsDivider);
            weMail.Vue.component('customizer-content-settings-video', settingsVideo);
            weMail.Vue.component('customizer-content-settings-footer', settingsFooter);
        },

        mounted() {
            const vm = this;

            weMail.setCustomizerContentComponents(vm.context, {
                contentText,
                contentImage,
                contentImageGroup,
                contentImageCaption,
                contentSocialFollow,
                contentButton,
                contentDivider,
                contentVideo,
                contentFooter
            });

            const iframeInstance = new weMail.Vue(Iframe(vm));

            const INTERVAL = 300;

            const interval = setInterval(() => {
                if (window.frames.wemail_customizer_iframe.document.readyState === 'complete') {
                    iframeInstance.$mount(window.frames.wemail_customizer_iframe.window.document.getElementById('wemail-customizer'));
                    clearInterval(interval);
                }
            }, INTERVAL);
        }
    };
</script>

<style lang="scss">
    #wemail-customizer {
        position: fixed;
        top: 42px;
        left: 0;
        width: calc(100% - 202px);
        margin-left: 182px;
    }

    #customizer-container {
        height: calc(100vh - 138px);
        margin-top: 8px;
        background-color: #fff;
        border: 1px solid $wp-border-color;
    }

    #customizer-stage {
        position: relative;
        width: 100%;
    }

    #wemail-customizer-iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #fff;
    }
</style>
