<script>
    export default {
        props: {
            html: {
                type: String,
                required: true
            },

            scope: {
                type: Object,
                required: false,
                default() {
                    return this.$parent;
                }
            }
        },

        render() {
            return Vue.compile(this.html).render.call(this.scope);
        }
    };
</script>
