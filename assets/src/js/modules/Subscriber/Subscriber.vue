<template>
    <div v-if="!isFetchingData">
        <h1>Single Subscriber component</h1>
        <pre>{{ subscriber }}</pre>
    </div>
</template>

<script>
    export default {
        routeName: 'subscriber',

        mixins: weMail.getMixins('routeComponent'),

        data() {
            return {
                subscriberId: 0
            };
        },

        computed: {
            ...weMail.Vuex.mapState('subscriber', ['subscriber'])
        }
    };
</script>
