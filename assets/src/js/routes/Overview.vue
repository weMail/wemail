<template>
    <div>
        <p>Overview component</p>
        <partial-overview></partial-overview>
    </div>
</template>

<script>
    import PartialOverview from '../components/partial-overview.vue';

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
