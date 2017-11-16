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
            <div :class="popoverBodyClass">
                <template v-if="controlType === 'multi-text'">
                    <div class="row">
                        <div v-for="option in options" :class="multiTextColumnClass">
                            <label>
                                <strong class="d-block">{{ option.label }}</strong>
                                <input
                                    type="text"
                                    class="form-control"
                                    :value="value[option.name]"
                                    @input="updateValue($event.target.value, option.name)"
                                    @keyup.enter="save"
                                >
                            </label>
                        </div>
                    </div>
                </template>

                <template v-else-if="controlType === 'text'">
                    <input
                        type="text"
                        class="form-control"
                        :value="value"
                        @input="updateValue($event.target.value)"
                        @keyup.enter="save"
                    >
                </template>

                <template v-else-if="controlType === 'radio'">
                    <ul class="list-inline no-margin">
                        <li v-for="option in options" class="list-inline-item">
                            <label>
                                <input
                                    type="radio"
                                    :value="option.name"
                                    :checked="newValue === option.value"
                                    @click.prevent="updateValue(option.value)"
                                > {{ option.label }}
                            </label>
                        </li>
                    </ul>
                </template>

                <template v-else-if="controlType === 'select'">
                    <select class="form-control" @change="updateValue($event.target.value)">
                        <option
                            v-for="option in options"
                            :value="option.value"
                            :selected="newValue === option.value"
                        >{{ option.label }}</option>
                    </select>
                </template>

                <em v-if="hint" class="wemail-popover-body-hint">{{ hint }}</em>
            </div>
            <div class="wemail-popover-footer">
                <button type="button" class="button button-small button-link" @click="show = false">{{ i18n.cancel }}</button>
                <button type="button" class="button button-small button-primary" @click="save">{{ i18n.save }}</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: [String, Object],
                required: true
            },

            type: {
                type: String,
                required: false,
                default: 'text'
            },

            options: {
                type: Array,
                required: false,
                default() {
                    return [];
                }
            },

            inline: {
                type: Boolean,
                required: false,
                default: true
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
            },

            hint: {
                type: String,
                required: false,
                default: ''
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
            },

            popoverBodyClass() {
                const classes = ['wemail-popover-body'];

                if (this.hint) {
                    classes.push('has-hint');
                }

                return classes;
            },

            controlType() {
                let type = 'text';

                if (this.type === 'text' && _.isObject(this.value)) {
                    type = 'multi-text';
                } else {
                    type = this.type;
                }

                return type;
            },

            multiTextColumnClass() {
                const TWELVE_COLUMN = 12;
                const column = Math.floor(TWELVE_COLUMN / this.options.length);

                return [`col-${column}`];
            }
        },

        mounted() {
            // Hide popover when click outside the container
            const vm = this;

            $(document).on('click', () => {
                vm.show = false;
            });

            // If we stop propagate here, other opened popover will not receive the click event,
            // which will prevent them from hiding. So instead, we will fire a custom vue event to
            // tell each editor to check which popover should be open and close the others.
            $(vm.$el).on('click', (e) => {
                weMail.event.$emit('hide-other-inline-editor', e, this._uid);
            });

            weMail.event.$on('hide-other-inline-editor', (e, _uid) => {
                if (this._uid === _uid) {
                    e.stopPropagation();
                } else {
                    vm.show = false;
                }
            });
        },

        watch: {
            show: 'focusEditor'
        },

        methods: {
            updateValue(value, name) {
                let newValue = value;

                if (this.controlType === 'multi-text' && name) {
                    newValue = $.extend(true, {}, this.newValue);
                    newValue[name] = value;
                }

                this.newValue = newValue;
            },

            save() {
                this.$emit('input', this.newValue);
                this.show = false;
            },

            focusEditor(show) {
                if (show) {
                    const vm = this;

                    this.$nextTick(() => {
                        if (this.controlType === 'multi-text') {
                            this.newValue = $.extend(true, {}, this.value);
                        } else {
                            this.newValue = this.value;
                        }

                        $(vm.$el).find('input.form-control').first().focus();
                    });
                }
            }
        }
    };
</script>

<style lang="scss">
    .wemail-popover-container {
        position: relative;
        display: inline;

        .wemail-popover-content {
            display: inline;

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

            .wemail-popover-body {
                padding: 8px;

                &.has-hint {
                    padding-bottom: 0;
                }

                label {

                    strong {
                        font-size: 12px;
                    }
                }

                .wemail-popover-body-hint {
                    font-size: 11px;
                    color: #999;
                }
            }

            .wemail-popover-footer {
                padding: 3px 8px;
                text-align: right;
                background-color: $wp-body-bg;
                border-top: 1px solid $wp-input-border-color;
            }
        }
    }
</style>
