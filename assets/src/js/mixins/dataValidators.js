export default {
    methods: {
        isEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        isEmpty(input) {
            return !input.trim();
        }
    }
};
