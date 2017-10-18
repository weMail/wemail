<template>
    <div class="section-settings-controls">
        <div v-show="editingSection === 'global'">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.backgroundColor }}
                    <span class="property-value">{{ template.globalCss.backgroundColor ? template.globalCss.backgroundColor : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="template.globalCss.backgroundColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.borderTop }}
                    <span class="property-value">
                        {{ template.globalCss.borderTopWidth ? template.globalCss.borderTopWidth : '0px' }} &nbsp;
                        {{ template.globalCss.borderColor ? template.globalCss.borderColor : '######' }}
                    </span>
                </h4>
                <div class="property">
                    <input-range v-model="globalBorderTopWidth" min="0" max="10"></input-range>
                    <br><br>
                    <color-picker v-model="template.globalCss.borderColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.fontFamily }}
                </h4>
                <div class="property">
                    <select v-model="template.globalCss.fontFamily" class="form-control">
                        <option v-for="fontFamily in fontFamilies" :value="fontFamily.id">{{ fontFamily.text }}</option>
                    </select>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.fontSize }}
                    <span class="property-value">
                        {{ template.globalCss.fontSize ? template.globalCss.fontSize : '0px' }}
                    </span>
                </h4>
                <div class="property">
                    <input-range v-model="globalFontSize" min="10" max="25"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.textColor }}
                    <span class="property-value">
                        {{ template.globalCss.color ? template.globalCss.color : '######' }}
                    </span>
                </h4>
                <div class="property">
                    <color-picker v-model="template.globalCss.color"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.linkColor }}
                    <span class="property-value">
                        {{ template.globalElementStyles.a.color ? template.globalElementStyles.a.color : '######' }}
                    </span>
                </h4>
                <div class="property">
                    <color-picker v-model="template.globalElementStyles.a.color"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.linkUnderline }}
                </h4>
                <div class="property">
                    <ul class="list-inline text-center">
                        <li class="list-inline-item">
                            <label>
                                <input type="radio" value="underline" v-model="template.globalElementStyles.a.textDecoration"> {{ i18n.underline }}
                            </label>
                        </li>
                        <li class="list-inline-item">
                            <label>
                                <input type="radio" value="none" v-model="template.globalElementStyles.a.textDecoration"> {{ i18n.none }}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <template v-for="section in sections">
            <div v-show="editingSection === section">
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
                        {{ i18n.textColor }}
                        <span class="property-value">{{ style.color ? style.color : '######' }}</span>
                    </h4>
                    <div class="property">
                        <color-picker v-model="style.color"></color-picker>
                    </div>
                </div>

                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.marginBottom }}
                        <span class="property-value">
                            {{ style.marginBottom ? style.marginBottom : '0px' }}
                        </span>
                    </h4>
                    <div class="property">
                        <input-range v-model="sectionMarginBottom" min="0" max="50"></input-range>
                    </div>
                </div>

                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.borderTop }}
                        <span class="property-value">
                            {{ style.borderTopWidth ? style.borderTopWidth : '0px' }} &nbsp;
                            {{ style.borderTopColor ? style.borderTopColor : '######' }}
                        </span>
                    </h4>
                    <div class="property">
                        <input-range v-model="sectionBorderTopWidth" min="0" max="10"></input-range>
                        <br><br>
                        <color-picker v-model="style.borderTopColor"></color-picker>
                    </div>
                </div>

                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.borderBottom }}
                        <span class="property-value">
                            {{ style.borderBottomWidth ? style.borderBottomWidth : '0px' }} &nbsp;
                            {{ style.borderBottomColor ? style.borderBottomColor : '######' }}
                        </span>
                    </h4>
                    <div class="property">
                        <input-range v-model="sectionBorderBottomWidth" min="0" max="10"></input-range>
                        <br><br>
                        <color-picker v-model="style.borderBottomColor"></color-picker>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            i18n: {
                type: Object,
                required: true
            },

            template: {
                type: Object,
                required: true
            },

            editingSection: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                sections: ['top', 'head', 'body', 'footer'],
                fontFamilies: [
                    {
                        id: 'inherit',
                        text: this.i18n.inherit
                    },
                    {
                        id: 'arial,helvetica,sans-serif',
                        text: 'Arial'
                    },
                    {
                        id: 'comic sans ms,sans-serif',
                        text: 'Comic Sans MS'
                    },
                    {
                        id: 'courier new,courier',
                        text: 'Courier New'
                    },
                    {
                        id: 'georgia,palatino',
                        text: 'Georgia'
                    },
                    {
                        id: 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        text: 'Lucida'
                    },
                    {
                        id: 'tahoma,arial,helvetica,sans-serif',
                        text: 'Tahoma'
                    },
                    {
                        id: 'times new roman,times',
                        text: 'Times New Roman'
                    },
                    {
                        id: 'trebuchet ms,geneva',
                        text: 'Trebuchet MS'
                    },
                    {
                        id: 'verdana,geneva',
                        text: 'Verdana'
                    }
                ]
            };
        },

        computed: {

            style() {
                let section = this.editingSection;

                if (this.editingSection === 'global') {
                    section = 'top';
                }

                return _(this.template.sections)
                    .filter({
                        name: section
                    })
                    .head()
                    .wrapperStyle;
            },

            globalBorderTopWidth: {
                get() {
                    return parseInt(this.template.globalCss.borderTopWidth, 10);
                },

                set(value) {
                    this.template.globalCss.borderTopWidth = `${value}px`;
                }
            },

            globalFontSize: {
                get() {
                    return parseInt(this.template.globalCss.fontSize, 10);
                },

                set(value) {
                    this.template.globalCss.fontSize = `${value}px`;
                }
            },

            sectionMarginBottom: {
                get() {
                    return parseInt(this.style.marginBottom, 10);
                },

                set(value) {
                    this.style.marginBottom = `${value}px`;
                }
            },

            sectionBorderTopWidth: {
                get() {
                    return parseInt(this.style.borderTopWidth, 10);
                },

                set(newVal) {
                    this.style.borderTopWidth = `${newVal}px`;
                }
            },

            sectionBorderBottomWidth: {
                get() {
                    return parseInt(this.style.borderBottomWidth, 10);
                },

                set(newVal) {
                    this.style.borderBottomWidth = `${newVal}px`;
                }
            }
        }
    };
</script>

<style lang="scss">
    .section-settings-controls {
        position: absolute;
        top: 60px;
        left: 0;
        width: 100%;
        height: calc(100% - 100px);
        overflow-y: auto;
    }
</style>
