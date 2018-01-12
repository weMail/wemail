<template>
    <div id="wemail-list-create">
        <list-modal :list="list"></list-modal>
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
        // routeName: 'listsCreate',

        data() {
            return {
                list: {
                    name: '',
                    description: '',
                    type: 'public'
                }
            };
        },

        components: {
            ListModal
        }
    };
</script>
