<template>
    <div v-if="isLoaded" id="wemail-list-edit">
        <list-modal :list="list" :edit="true"></list-modal>
    </div>
</template>

<script>
    /* eslint-disable global-require */

    function ListModal(resolve) {
        require.ensure(['./components/ListModal.vue'], () => {
            resolve(require('./components/ListModal.vue'));
        });
    }

    export default {
        name: 'listsEdit',

        apiEndpoint: '/lists/:id',

        expects: ['list'],

        components: {
            ListModal
        },

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('listsEdit', ['list'])
        }
    };
</script>
