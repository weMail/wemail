<template>
    <div>
        <p>Overview component</p>
        <p class="test-class">Hello World</p>
        <p class="test-class2">Test Class 2</p>
        <partial-overview></partial-overview>
    </div>
</template>

<script>
    import PartialOverview from './partial.vue';

    export default {
        components: {
            PartialOverview
        },

        data() {
            return {
                startDate: 'strdt'
            };
        },

        computed: {
            ...weMail.Vuex.mapState(['modelA', 'modelB'])
        }
    };
</script>

<style lang="scss">
    .test-class2 {
        color: $wp-yellow;
    }
</style>
