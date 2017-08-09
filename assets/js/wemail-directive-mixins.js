;(function($) {
    'use strict';

    Vue.directive('wemail-datepicker', {
        bind: function (el, binding, vnode) {
            $(el).datepicker();
        }
    });

    weMail.mixins.partials = {
        render: function (createElement) {
            var vm = this;

            if (!this.partials.length) {
                return;
            }

            var partials = this.partials.map(function (partial) {
                var content = partial.content ? vm.replaceWithData(partial.content) : '';
                return createElement(partial.tag, {
                    attrs: partial.attrs
                }, content);
            });

            return createElement('div', partials);
        },

        methods: {
            replaceWithData: function (str) {
                var containerComponent = this.$parent;
                var re = /{{([a-zA-Z\s0-9]+)}}/ig;

                return str.replace(re, function (match, p1) {
                    return containerComponent[p1.trim()];
                });
            }
        }
    }

})(jQuery);
