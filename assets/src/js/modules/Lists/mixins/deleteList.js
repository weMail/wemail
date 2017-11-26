export default {
    methods: {
        deleteList(id, callback) {
            const vm = this;
            const warn = {
                confirmButtonText: vm.i18n.delete,
                cancelButtonText: __('Cancel')
            };

            let api = weMail.api;

            if (_.isArray(id)) {
                warn.text = vm.i18n.deleteListsWarnMsg;
                api = api.lists().query({
                    ids: id
                });

            } else {
                warn.text = vm.i18n.deleteListWarnMsg;
                api.lists(id);
            }

            vm.warn(warn).then((deleteIt) => {
                if (deleteIt) {
                    api.delete().done((response) => {
                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    });
                }
            });
        }
    }
};
