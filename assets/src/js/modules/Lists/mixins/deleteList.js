export default {
    methods: {
        deleteList(id, callback) {
            const vm = this;
            const warn = {
                confirmButtonText: __('Delete'),
                cancelButtonText: __('Cancel')
            };

            let api = weMail.api;

            if (_.isArray(id)) {
                warn.text = __('Are you sure you want to delete these lists?');
                api = api.lists().query({
                    ids: id
                });

            } else {
                warn.text = __('Are you sure you want to delete this list?');
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
