<template>
    <div :class="containerClassNames">
        <div
            :class="['wemail-popover-content', show ? 'active' : '']"
            @click="show = true"
        >
            <slot></slot>
        </div>

        <div v-if="show" class="wemail-popover show wemail-popover-bottom" :style="popoverStyle">
            <div class="wemail-popover-arrow"></div>
            <div class="wemail-popover-close">
                <i class="fa fa-times-circle" @click="show = false"></i>
            </div>
            <div class="wemail-popover-body">
                <input
                    type="text"
                    class="form-control"
                    :value="value"
                    @input="updateValue($event.target.value)"
                    @keyup.enter="save"
                >

                <em class="wemail-popover-body-hint">{{ i18n.popoverFormHint }}</em>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: true
            },

            containerClass: {
                type: Array,
                required: false,
                default() {
                    return [];
                }
            },

            top: {
                type: Number,
                required: false,
                default: 0
            }
        },

        data() {
            return {
                show: false,
                newValue: ''
            };
        },

        computed: {
            i18n() {
                return weMail.i18n;
            },

            containerClassNames() {
                return ['wemail-popover-container'].concat(this.containerClass);
            },

            popoverStyle() {
                return {
                    top: `calc(100% - ${this.top}px)`
                };
            }
        },

        methods: {
            updateValue(value) {
                this.newValue = value;
            },

            save() {
                this.$emit('input', this.newValue);
                this.show = false;
            }
        }
    };
</script>

<style lang="scss">
    .wemail-popover-container {
        position: relative;

        .wemail-popover-content {

            & > * {
                cursor: pointer;

                &:hover {
                    background-color: rgba(0, 115, 170, 0.2);
                }
            }

            &.active > * {
                background-color: rgba(0, 115, 170, 0.2);
            }
        }

        .wemail-popover.wemail-popover-bottom {
            min-width: 310px;
            border-radius: 3px;

            .wemail-popover-arrow {
                left: 30px;
            }

            .wemail-popover-close {
                position: absolute;
                top: -7px;
                right: -7px;
                width: 15px;
                height: 15px;
                font-size: 18px;
                background-color: #fff;
                border-radius: 50%;
                box-shadow: 0 0 6px 2px rgba(255, 255, 255, 0.62);

                i {
                    position: absolute;
                    top: -1px;
                    color: #000;
                    cursor: pointer;
                }
            }

            .wemail-popover-body {
                padding: 8px 8px 0;

                .wemail-popover-body-hint {
                    font-size: 11px;
                    color: #999;
                }
            }
        }
    }
</style>
