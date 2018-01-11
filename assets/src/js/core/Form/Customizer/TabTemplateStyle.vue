<template>
    <div>
        <div class="customizer-sidebar-settings-fields no-save-button">
            <div class="control-property">
                <h4 class="property-title">
                    {{ i18n.backgroundColor }}
                    <span class="property-value">{{ style.backgroundColor ? style.backgroundColor : '######' }}</span>
                </h4>

                <div class="property">
                    <color-picker v-model="style.backgroundColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title">
                    {{ i18n.color }}
                    <span class="property-value">{{ style.color ? style.color : '######' }}</span>
                </h4>

                <div class="property">
                    <color-picker v-model="style.color"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.border }}
                    <span class="property-value">
                        {{ style.borderWidth ? style.borderWidth : '0px' }} &nbsp;
                        {{ style.borderColor ? style.borderColor : '######' }}
                    </span>
                </h4>
                <div class="property">
                    <input-range v-model="borderWidth" min="0" max="10"></input-range>
                    <br><br>
                    <color-picker v-model="style.borderColor"></color-picker>
                </div>
            </div>

            <h3 class="control-section-title">{{ i18n.fieldLabels }}</h3>

            <div class="control-property">
                <h4 class="property-title">{{ i18n.position }}</h4>

                <div class="property text-center">
                    <div class="button-group">
                        <button
                            v-for="position in label.positions"
                            :class="['button', (position === form.template.label.position) ? 'button-primary active' : '']"
                            @click="form.template.label.position = position"
                        >{{ i18n[position] }}</button>
                    </div>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title">{{ i18n.align }}</h4>

                <div class="property text-center">
                    <div class="button-group">
                        <button
                            v-for="align in label.aligns"
                            :class="['button', (align === form.template.label.align) ? 'button-primary active' : '']"
                            @click="form.template.label.align = align"
                        >{{ i18n[align] }}</button>
                    </div>
                </div>
            </div>

            <h3 class="control-section-title">{{ i18n.submitButton }}</h3>

            <div class="control-property">
                <h4 class="property-title">{{ i18n.label }}</h4>

                <div class="property">
                    <input type="text" class="form-control" v-model="submit.label">
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title">{{ i18n.position }}</h4>

                <div class="property text-center">
                    <div class="button-group">
                        <button
                            v-for="position in submitButton.positions"
                            :class="['button', (position === submit.position) ? 'button-primary active' : '']"
                            @click="submit.position = position"
                        >{{ i18n[position] }}</button>
                    </div>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title">{{ i18n.size }}</h4>

                <div class="property text-center">
                    <div class="button-group">
                        <button
                            v-for="size in submitButton.sizes"
                            :class="['button', (size === submit.size) ? 'button-primary active' : '']"
                            @click="submit.size = size"
                        >{{ i18n[size] }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            i18n: {
                type: Object,
                required: true
            },

            form: {
                type: Object,
                required: true
            },

            formFields: {
                type: Array,
                required: true
            },

            index: {
                type: Number,
                required: false
            }
        },

        data() {
            return {
                formStyles: ['inline', 'floating-bar', 'floating-box', 'modal'],
                types: ['inline', 'floating', 'modal'],
                layout: ['box', 'bar'],
                label: {
                    positions: ['top', 'left', 'right', 'hidden'],
                    aligns: ['left', 'right']
                },
                submitButton: {
                    positions: ['left', 'center', 'right'],
                    sizes: ['auto', 'block']
                }
            };
        },

        computed: {
            style() {
                return this.form.template.style;
            },

            submit() {
                return this.form.template.submitButton;
            },

            borderWidth: {
                get() {
                    return parseInt(this.style.borderWidth, 10);
                },

                set(value) {
                    this.style.borderWidth = `${value}px`;
                }
            }
        }
    };
</script>
