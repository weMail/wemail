export default {
    methods: {
        swal(defaults, ...args) {
            if (args[0] === undefined) {
                console.error('Alert expects at least 1 attribute!');
                return false;
            }

            args[0] = $.extend(true, defaults, args[0]);

            return swal2(...args).catch(swal2.noop);
        },

        alert(...args) {
            const defaults = {
                title: ''
            };

            return this.swal(defaults, ...args);
        },

        warn(...args) {
            const defaults = {
                type: 'warning',
                title: '',
                showCancelButton: true,
                confirmButtonText: '',
                cancelButtonText: '',
                confirmButtonColor: '#dc3232',
                cancelButtonColor: '#cccccc'
            };

            return this.swal(defaults, ...args);
        },

        success(...args) {
            const defaults = {
                type: 'success',
                title: '',
                confirmButtonText: '',
                confirmButtonColor: '#46b450'
            };

            return this.swal(defaults, ...args);
        }
    }
};
