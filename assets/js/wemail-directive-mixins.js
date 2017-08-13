;(function($) {
    'use strict';

    weMail.component('datepicker', {
        template: `<input type="text" :value="value" @input="updateValue($event.target.value)">`,
        props: ['value'],
        mounted: function () {
            var vm = this;

            $(this.$el).datepicker({
                onSelect: function (date) {
                    vm.updateValue(date);
                }
            });
        },

        methods: {
            updateValue(value) {
                this.$emit('input', value);
            }
        }
    });

    Vue.directive('wemail-datepicker', {
        bind: function (el, binding, vnode) {
            $(el).datepicker({
                onSelect: function (date) {
                    Vue.set(vnode.context, binding.expression, date);
                }
            });
        }
    });

    weMail.mixins.partials = {
        render: function (createElement) {
            var vm = this;

            if (!this.partials.length) {
                return;
            }

            window.ediamin = this;

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
