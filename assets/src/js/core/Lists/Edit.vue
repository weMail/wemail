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
        routeName: 'listsEdit',

        components: {
            ListModal
        },

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('listsEdit', ['list'])
        }
    };
</script>
