<template>
    <div>image caption settings</div>
</template>

<script>
    export default {
        props: {
            currentTab: {
                type: String,
                required: true
            },

            i18n: {
                type: Object,
                required: true
            },

            sectionIndex: {
                type: Number,
                required: true
            },

            contentIndex: {
                type: Number,
                required: true
            }
        }
    };
</script>
