<script>
    export default {
        props: {
            action: {
                type: String,
                required: true
            }
        },

        render(createElement) {
            const vm = this;
            const actions = weMail.actions[this.action];

            if (actions && actions.length) {
                const component = actions.map((action) => {
                    const content = action.content ? vm.replaceWithData(action.content) : '';

                    return createElement(action.tag, {
                        attrs: action.attrs
                    }, content);
                });

                return createElement('div', component);
            }

            return false;
        },

        methods: {
            // Enable partials to use parent component data
            replaceWithData(str) {
                const parentComponent = this.$parent;
                const re = /{{([a-zA-Z\s0-9]+)}}/ig;

                return str.replace(re, (match, p1) => {
                    return parentComponent[p1.trim()];
                });
            }
        }
    };
</script>
