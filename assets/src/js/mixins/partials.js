export default {
    render(createElement) {
        const vm = this;
        const partialComponentName = vm.$options.name.replace('partial-', '');
        const partials = weMail.partials[partialComponentName];

        if (partials.length) {
            const partialComponents = partials.map((partial) => {
                const content = partial.content ? vm.replaceWithData(partial.content) : '';
                return createElement(partial.tag, {
                    attrs: partial.attrs
                }, content);
            });

            return createElement('div', partialComponents);
        }

        return false;
    },

    methods: {
        // Enable partials to use parent component data
        replaceWithData(str) {
            const containerComponent = this.$parent;
            const re = /{{([a-zA-Z\s0-9]+)}}/ig;

            return str.replace(re, (match, p1) => {
                return containerComponent[p1.trim()];
            });
        }
    }
};
