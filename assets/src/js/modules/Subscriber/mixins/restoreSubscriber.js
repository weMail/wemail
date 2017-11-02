export default {
    methods: {
        restoreSubscriber(id, callback) {
            let api = weMail.api;

            if (_.isArray(id)) {
                api = api.subscribers().restore()
                    .bulk()
                    .query({
                        ids: id
                    });
            } else {
                api = api.subscribers().restore(id);
            }

            api.put().done((response) => {
                if (typeof callback === 'function') {
                    callback(response);
                }
            });
        }
    }
};
