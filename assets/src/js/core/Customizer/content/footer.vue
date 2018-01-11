<template>
    <content-text
        :content="content"
        :global-css="globalCss"
        :customizer="customizer"
    ></content-text>
</template>

<script>
    import ContentText from './text.vue';

    export default {
        components: {
            ContentText
        },

        props: {
            content: {
                type: Object,
                required: true
            },

            globalCss: {
                type: Object,
                required: true
            },

            customizer: {
                type: Object,
                required: false
            }
        }
    };
</script>
