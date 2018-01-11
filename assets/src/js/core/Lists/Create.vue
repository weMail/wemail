<template>
    <div v-if="isLoaded" id="wemail-list-create">
        <list-modal :i18n="i18n" :list="list"></list-modal>
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
        routeName: 'listsCreate',

        components: {
            ListModal
        },

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('listsCreate', ['i18n', 'list'])
        }
    };
</script>
