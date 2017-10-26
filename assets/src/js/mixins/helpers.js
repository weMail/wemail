export default {
    methods: {
        snakeKeys(obj) {
            return _.mapKeys(obj, (value, key) => {
                return _.snakeCase(key);
            });
        }
    }
};
