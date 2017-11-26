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
                confirmButtonText: __('OK'),
                cancelButtonText: __('Cancel'),
                confirmButtonColor: '#dc3232',
                cancelButtonColor: '#cccccc'
            };

            return this.swal(defaults, ...args);
        },

        success(...args) {
            if (typeof args[0] === 'string') {
                args[0] = {
                    text: args[0]
                };
            }

            const defaults = {
                type: 'success',
                title: '',
                confirmButtonText: __('OK'),
                confirmButtonColor: '#46b450'
            };

            return this.swal(defaults, ...args);
        },

        error(...args) {
            if (typeof args[0] === 'string') {
                args[0] = {
                    text: args[0]
                };
            }

            const defaults = {
                type: 'error',
                title: '',
                confirmButtonText: __('OK')
            };

            return this.swal(defaults, ...args);
        }
    }
};
