<template>
    <div class="content-settings-container settings-button">
        <div v-if="settingsTab === 'content'" class="settings-button-content">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.buttonText }}
                </h4>
                <div class="property">
                    <input type="text" class="form-control" v-model="content.text">
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.buttonLink }}
                </h4>
                <div class="property">
                    <input type="text" class="form-control" v-model="content.href">
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.titleAttribute }}
                </h4>
                <div class="property">
                    <input type="text" class="form-control" v-model="content.title">
                </div>
            </div>
        </div>
        <div v-if="settingsTab === 'style'">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.backgroundColor }}
                    <span class="property-value">{{ style.backgroundColor ? style.backgroundColor : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="style.backgroundColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.fontColor }}
                    <span class="property-value">{{ style.color ? style.color : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="style.color"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.fontSize }}
                    <span class="property-value">{{ style.fontSize ? style.fontSize : '14px' }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="fontSize" min="10" max="80"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.paddingTopBottom }}
                    <span class="property-value">{{ style.paddingTop }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="paddingTopBottom" min="8" max="50"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.paddingLeftRight }}
                    <span class="property-value">{{ style.paddingLeft }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="paddingLeftRight" min="8" max="150"></input-range>
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

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.borderRadius }}
                    <span class="property-value">{{ style.borderRadius }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="borderRadius" min="0" max="100"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.upperCase }}
                </h4>
                <div class="property">
                    <label><input type="checkbox" v-model="upperCase"> {{ i18n.yes }}</label>
                </div>
            </div>
        </div>

        <div v-if="settingsTab === 'settings'">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.width }}
                </h4>
                <div class="property">
                    <ul class="list-inline no-margin">
                        <li class="list-inline-item">
                            <label><input type="radio" value="inline-block" v-model="style.display"> {{ i18n.default }}</label>
                        </li>
                        <li class="list-inline-item">
                            <label><input type="radio" value="block" v-model="style.display"> {{ i18n.block }}</label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.containerBackground }}
                    <span class="property-value">{{ containerStyle.backgroundColor ? containerStyle.backgroundColor : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="containerStyle.backgroundColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.align }}
                    <span class="property-value">
                        {{ containerStyle.textAlign ? i18n[containerStyle.textAlign] : i18n.center }} &nbsp;
                    </span>
                </h4>
                <div class="property text-center">
                    <div class="button-group button-group-extra-padding">
                        <button
                            v-for="textAlign in textAligns"
                            type="button"
                            :class="['button', (containerStyle.textAlign === textAlign) ? 'active' : '']"
                            @click="containerStyle.textAlign = textAlign"
                        >
                            <i :class="['fa fa-align-' + textAlign]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.containerPadding }}
                    <span class="property-value">{{ containerStyle.padding }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="containerPadding" min="0" max="100"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.containerBottomMargin }}
                    <span class="property-value">{{ containerStyle.marginBottom }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="containerMarginBottom" min="0" max="100"></input-range>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            settingsTab: {
                type: String,
                required: true
            },

            i18n: {
                type: Object,
                required: true
            },

            sectionIndex: {
                type: Number,
                required: true
            },

            contentIndex: {
                type: Number,
                required: true
            },

            content: {
                type: Object,
                required: true
            },

            customizer: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                textAligns: ['left', 'center', 'right']
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            containerStyle() {
                return this.content.containerStyle;
            },

            fontSize: {
                get() {
                    return parseInt(this.style.fontSize, 10);
                },

                set(value) {
                    this.style.fontSize = `${value}px`;
                }
            },

            paddingTopBottom: {
                get() {
                    return parseInt(this.style.paddingTop, 10);
                },

                set(value) {
                    this.style.paddingTop = `${value}px`;
                    this.style.paddingBottom = `${value}px`;
                }
            },

            paddingLeftRight: {
                get() {
                    return parseInt(this.style.paddingLeft, 10);
                },

                set(value) {
                    this.style.paddingLeft = `${value}px`;
                    this.style.paddingRight = `${value}px`;
                }
            },

            borderWidth: {
                get() {
                    return parseInt(this.style.borderWidth, 10);
                },

                set(value) {
                    this.style.borderWidth = `${value}px`;
                }
            },

            borderRadius: {
                get() {
                    return parseInt(this.style.borderRadius, 10);
                },

                set(value) {
                    this.style.borderRadius = `${value}px`;
                }
            },

            upperCase: {
                get() {
                    return this.style.textTransform === 'uppercase' || false;
                },

                set(isUpperCase) {
                    this.style.textTransform = isUpperCase ? 'uppercase' : 'none';
                }
            },

            containerPadding: {
                get() {
                    return parseInt(this.containerStyle.padding, 10);
                },

                set(value) {
                    this.containerStyle.padding = `${value}px`;
                }
            },

            containerMarginBottom: {
                get() {
                    return parseInt(this.containerStyle.marginBottom, 10);
                },

                set(value) {
                    this.containerStyle.marginBottom = `${value}px`;
                }
            }
        }
    };
</script>
