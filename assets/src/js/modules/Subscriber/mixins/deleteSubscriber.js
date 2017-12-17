export default {
    methods: {
        deleteSubscriber(id, callback, permaDelete) {
            const vm = this;
            const warn = {
                confirmButtonText: vm.i18n.delete,
                cancelButtonText: __('Cancel')
            };

            let api = weMail.api;

            if (_.isArray(id)) {
                warn.text = vm.i18n.deleteSubsWarnMsg;
                api = api.subscribers().query({
                    id
                });
            } else {
                warn.text = vm.i18n.deleteSubWarnMsg;
                api = api.subscribers(id);
            }

            if (permaDelete) {
                vm.warn(warn).then((deleteIt) => {
                    if (deleteIt) {
                        api.query({
                            permanent: true
                        }).delete().done((response) => {
                            // this success alert will be replaced by some notification library
                            vm.success({
                                text: vm.i18n.subscriberDeleted,
                                confirmButtonText: vm.i18n.close
                            }).then(() => {
                                if (typeof callback === 'function') {
                                    callback(response);
                                }
                            });
                        });
                    } else {
                        api.reset();
                    }
                });

            } else {
                api.delete().done((response) => {
                    if (typeof callback === 'function') {
                        callback(response);
                    }
                });
            }
        }
    }
};
