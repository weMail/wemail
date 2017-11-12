<template>
    <input type="text" :value="value">
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: true,
                default: ''
            },

            defaultColor: {
                type: String,
                required: false,
                default: ''
            }
        },

        mounted() {
            const vm = this;

            $(vm.$el).wpColorPicker({
                color: vm.color,

                change(e, ui) {
                    vm.$emit('input', ui.color.toString());
                },

                clear() {
                    vm.$emit('input', vm.defaultColor);
                }
            });
        }
    };
</script>

<style lang="scss">
    .wemail a.wp-color-result {
        box-sizing: content-box;
    }

    .colorpicker-grouped {
        position: relative;

        .wp-color-result.wp-picker-open:after {
            content: "";
        }

        .wp-picker-holder {
            position: absolute;
            top: 30px;
            right: 0;
            z-index: 1;
        }

        .wp-picker-container .iris-picker {
            margin-top: 0;
        }
    }
</style>
