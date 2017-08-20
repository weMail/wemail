export default {
    bind(el, binding, vnode) {
        $(el).datepicker({
            onSelect(date) {
                weMail.Vue.set(vnode.context, binding.expression, date);
            }
        });
    }
};
