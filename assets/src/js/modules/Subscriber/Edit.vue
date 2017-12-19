<template>
    <div v-if="isLoaded" id="wemail-subscriber-edit">
        <subscriber-modal :subscriber="subscriberData" :edit="true"></subscriber-modal>
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
        routeName: 'subscriberEdit',

        components: {
            SubscriberModal
        },

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('subscriberEdit', ['subscriber']),

            subscriberData() {
                const subscriber = {};

                if (this.subscriber) {
                    subscriber.email = this.subscriber.email;
                    subscriber.first_name = this.subscriber.first_name;
                    subscriber.last_name = this.subscriber.last_name;
                    subscriber.phone = this.subscriber.phone;

                    subscriber.lists = this.subscriber.lists.filter((list) => {
                        return list.status === 'subscribed';
                    }).map((list) => {
                        return list.id;
                    });
                }

                return subscriber;
            }
        }
    };
</script>
