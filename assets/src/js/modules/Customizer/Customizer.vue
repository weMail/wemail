<template>
    <div id="wemail-customizer">
        <slot name="header"></slot>

        <div id="customizer-container" class="d-flex">
            <div id="customizer-stage">
                 <iframe :src="iframeSource" frameborder="0" name="wemail_customizer_iframe" id="wemail-customizer-iframe"></iframe>
            </div>

            <settings
                :i18n="customizer.i18n"
                :context="context"
                :content-types="customizer.contentTypes"
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

        mounted() {
            const customizer = this;

            weMail.setCustomizerContentComponents(customizer.context, {
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

            const iframeInstance = new weMail.Vue(Iframe(customizer));

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
