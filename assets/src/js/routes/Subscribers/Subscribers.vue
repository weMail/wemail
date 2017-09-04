<template>
    <div>
        Subscribers component
    </div>
</template>

<script>
    export default {
        routeName: 'subscribers',

        mixins: [weMail.mixins.routeComponent],

        computed: {
            ...weMail.Vuex.mapState('subscribers')
        }
    };
</script>
