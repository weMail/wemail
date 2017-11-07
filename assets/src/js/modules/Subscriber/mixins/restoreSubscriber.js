export default {
    methods: {
        restoreSubscriber(id, callback) {
            let api = weMail.api;

            if (_.isArray(id)) {
                api = api.subscribers().restore()
                    .query({
                        ids: id
                    });
            } else {
                api = api.subscribers(id).restore();
            }

            api.put().done((response) => {
                if (typeof callback === 'function') {
                    callback(response);
                }
            });
        }
    }
};
