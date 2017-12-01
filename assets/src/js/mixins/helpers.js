import md5 from 'md5';

export default {
    methods: {
        snakeKeys(obj) {
            return _.mapKeys(obj, (value, key) => {
                return _.snakeCase(key);
            });
        },

        toWPDateTime(dateTime, format = weMail.momentDateFormat) {
            return moment
                .tz(dateTime, moment.defaultFormat, 'Etc/UTC')
                .tz(weMail.dateTime.server.timezone)
                .format(format);
        },

        toMomentDateTime(dateTime, format = 'YYYY-MM-DD HH:mm:ss') {
            return moment
                .tz(dateTime, 'YYYY-MM-DD HH:mm:ss', weMail.dateTime.server.timezone)
                .tz('Etc/UTC')
                .format(moment.defaultFormat);
        },

        md5(string) {
            return md5(string);
        }
    }
};
