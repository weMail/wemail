<template>
    <div id="wemail-subscriber-create">
        <subscriber-modal :subscriber="subscriber"></subscriber-modal>
    </div>
</template>

<script>
    /* eslint-disable global-require */

    function SubscriberModal(resolve) {
        require.ensure(['./components/SubscriberModal.vue'], () => {
            resolve(require('./components/SubscriberModal.vue'));
        });
    }

    export default {
        components: {
            SubscriberModal
        },

        data() {
            return {
                subscriber: {
                    email: '',
                    first_name: '',
                    last_name: '',
                    phone: '',
                    lists: []
                }
            };
        }
    };
</script>
