<template>
    <customizer-content-settings-text
        :settings-tab="settingsTab"
        :section-index="sectionIndex"
        :content-index="contentIndex"
        :content="content"
        :customizer="customizer"
    ></customizer-content-settings-text>
</template>

<script>
    import CustomizerContentSettingsText from './text.vue';

    export default {
        components: {
            CustomizerContentSettingsText
        },

        props: {
            settingsTab: {
                type: String,
                required: true
            },

            sectionIndex: {
                type: Number,
                required: true
            },

            contentIndex: {
                type: Number,
                required: true
            },

            content: {
                type: Object,
                required: true
            },

            customizer: {
                type: Object,
                required: true
            }
        }
    };
</script>
