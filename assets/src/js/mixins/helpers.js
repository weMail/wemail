export default {
    methods: {
        snakeKeys(obj) {
            return _.mapKeys(obj, (value, key) => {
                return _.snakeCase(key);
            });
        },

        toWPDate(dateTime) {
            return moment
                .tz(dateTime, 'YYYY-MM-DD HH:mm:ss', 'Etc/UTC')
                .tz(weMail.dateTime.server.timezone)
                .format(`${weMail.momentDateFormat}`);
        }
    }
};
