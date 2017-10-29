export default {
    methods: {
        deleteSubscriber(subscriber, callback) {
            const vm = this;

            vm.warn({
                text: vm.i18n.deleteSubWarnMsg,
                confirmButtonText: vm.i18n.delete,
                cancelButtonText: vm.i18n.cancel
            }).then((deleteIt) => {
                if (deleteIt) {
                    weMail.api
                        .subscribers(subscriber.id)
                        .delete()
                        .done(() => {
                            if (typeof callback === 'function') {
                                callback();
                            }
                        });
                }
            });
        }
    }
};
