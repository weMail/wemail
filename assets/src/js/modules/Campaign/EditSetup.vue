<template>
    <div>
        <h1>{{ __('Edit Campaign') }}</h1>
        <setup namespace="campaignEdit"></setup>
    </div>
</template>

<script>
    import Setup from './Setup.vue';

    export default {
        namespace: 'campaignEdit',

        components: {
            Setup
        }
    };
</script>
