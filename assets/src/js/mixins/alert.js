export default {
    methods: {
        alert(...args) {
            if (args[0] === undefined) {
                console.error('Alert expects at least 1 attribute!');
                return false;
            }

            const defaults = {
                title: ''
            };

            args[0] = $.extend(true, defaults, args[0]);

            return swal2(...args).catch(swal2.noop);
        }
    }
};
