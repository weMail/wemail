<template>
    <div id="customizer-settings">
        <template>
            <div v-show="currentWindow !== 'content-settings'" class="customizer-settings-header">
                <div class="button-group">
                    <button
                        type="button"
                        :class="['button', currentWindow === 'contentTypes' ? 'active button-primary' : '']"
                        @click="currentWindow = 'contentTypes'"
                    >{{ i18n.content }}</button>

                    <button
                        type="button"
                        :class="['button', currentWindow === 'design' ? 'active button-primary' : '']"
                        @click="currentWindow = 'design'"
                    >{{ i18n.design }}</button>
                </div>
            </div>

            <div v-show="currentWindow === 'contentTypes'" id="customizer-content-types">
                <table>
                    <tbody>
                        <tr v-for="type in contentTypes.types" data-source="settings" :data-content-type="type">
                            <td>
                                <div class="button" :style="getContentTypeBtnStyle(type)">{{ i18n[type] }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-show="currentWindow === 'design'" id="customizer-design">
                <label>Global font size <input type="text" v-model="template.globalCss.fontSize"></label>
                page/header etc
                <div v-for="section in template.sections">
                    <h4>{{ section.name }}</h4>
                    <pre v-for="content in section.contents">{{ content.type }}</pre>
                </div>
            </div>
        </template>

        <template>
            <div
                v-if="currentWindow === 'content-settings'"
                :class="headerClass"
            >
                <h3>{{ i18n[content.type] }}</h3>

                <div class="button-group">
                    <button
                        type="button"
                        :class="['button', settingsTab === 'content' ? 'active button-primary' : '']"
                        @click="settingsTab = 'content'"
                    >{{ i18n.content }}</button>
                    <button
                        v-if="hasStyleTab"
                        type="button"
                        :class="['button', settingsTab === 'style' ? 'active button-primary' : '']"
                        @click="settingsTab = 'style'"
                    >{{ i18n.style }}</button>
                    <button
                        v-if="hasSettingsTab"
                        type="button"
                        :class="['button', settingsTab === 'settings' ? 'active button-primary' : '']"
                        @click="settingsTab = 'settings'"
                    >{{ i18n.settings }}</button>
                </div>
            </div>

            <div v-if="currentWindow === 'content-settings'" class="content-settings-controls">
                <component
                    :is="getSettingsComponentName()"
                    :settings-tab="settingsTab"
                    :i18n="i18n"
                    :section-index="sectionIndex"
                    :content-index="contentIndex"
                    :content="content"
                    :customizer="customizer"
                ></component>
            </div>

            <button v-if="currentWindow === 'content-settings'" type="button" class="button button-link content-settings-bottom-btns" @click="saveAndClose">
                {{ i18n.saveAndClose }}
            </button>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            context: {
                type: String,
                required: true
            },

            customizer: {
                type: Object,
                required: true
            },

            template: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                currentWindow: 'contentTypes',
                content: {},
                settingsTab: 'content',
                sectionIndex: 0,
                contentIndex: 0
            };
        },

        computed: {
            i18n() {
                return this.customizer.i18n;
            },

            contentTypes() {
                return this.customizer.contentTypes;
            },

            hasSettingsTab() {
                const noSettingsTab = this.customizer.contentTypes.settings[this.content.type].noSettingsTab;
                return (noSettingsTab === undefined || !noSettingsTab);
            },

            hasStyleTab() {
                const noStyleTab = this.customizer.contentTypes.settings[this.content.type].noStyleTab;
                return (noStyleTab === undefined || !noStyleTab);
            },

            headerClass() {
                const classNames = ['customizer-settings-header', 'content-settings'];

                if (!this.hasSettingsTab) {
                    classNames.push('no-settings-tab');
                }

                if (!this.hasStyleTab) {
                    classNames.push('no-style-tab');
                }

                return classNames;
            }
        },

        created() {
            weMail.event.$on('open-content-settings', this.openContentSettings);
        },

        methods: {
            getContentTypeBtnStyle(type) {
                return {
                    backgroundImage: `url(${this.contentTypes.settings[type].image})`
                };
            },

            openContentSettings(sectionIndex, contentIndex) {
                this.content = this.template.sections[sectionIndex].contents[contentIndex];
                this.settingsTab = 'content';
                this.sectionIndex = sectionIndex;
                this.contentIndex = contentIndex;
                this.currentWindow = 'content-settings';
            },

            getSettingsComponentName() {
                return `customizer-content-settings-${weMail._.kebabCase(this.content.type)}`;
            },

            saveAndClose() {
                this.currentWindow = 'contentTypes';
            }
        }
    };
</script>

<style lang="scss">
    #customizer-settings {
        position: relative;
        min-width: 350px;
        max-width: 350px;
        overflow-x: hidden;
        overflow-y: auto;
        border-left: 1px solid $wp-border-color;
    }

    .customizer-settings-header {
        height: 60px;
        text-align: center;
        border-bottom: 1px solid $wp-border-color;

        &.content-settings {
            height: 106px;
            padding: 20px 0;
        }

        * {
            margin: 0;
        }

        h3 {
            font-size: 1.5em;
            font-weight: 200;
        }

        .button-group {
            margin-top: 15px;

            .button {
                padding: 0 25px;
            }
        }

        &.no-settings-tab {

            .button-group {

                .button {
                    padding: 0 40px;
                }
            }
        }
    }

    #customizer-content-types {

        table {
            width: 100%;
            padding: 0 2px;

            tr {
                display: inline-block;
                width: 33.3333333%;
                padding: 0 3px;
                margin-top: 5px;

                td {
                    display: block;
                    width: 100%;

                    .button {
                        width: 100%;
                        height: 110px;
                        padding: 84px 0 0;
                        font-size: 0.9em;
                        font-weight: 500;
                        line-height: 1;
                        text-align: center;
                        background-color: #fff;
                        background-repeat: no-repeat;
                        background-position: center 0;
                        background-size: 85px auto;

                        &:hover {
                            box-shadow: 0 0 3px rgba(0, 0, 0, 0.38);
                        }
                    }
                }
            }
        }
    }

    .content-settings-controls {
        position: absolute;
        top: 106px;
        left: 0;
        width: 100%;
        height: calc(100% - 140px);
        overflow-y: auto;
    }

    .content-settings-bottom-btns.button.button-link {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: auto;
        padding: 4px 0;
        color: #fff;
        text-align: center;
        background-color: $wp-blue;

        &:hover {
            background-color: $wp-blue-alt;
        }
    }

    .content-settings-container {

        .control-property {

            &:first-child .property-title {
                border-top: 0;
            }

            .wp-color-result {
                box-sizing: content-box;
                margin: 0;
            }
        }

        .property-title {
            padding: 10px 15px;
            margin: 0;
            background-color: $wp-body-bg;
            border-top: 1px solid $wp-border-color;
            border-bottom: 1px solid $wp-border-color;
        }

        .reset-property {

            a {
                font-weight: 400;
                text-decoration: none;
            }
        }

        .property-value {
            float: right;
        }

        .property {
            padding: 10px 15px;
            overflow-x: auto;
        }
    }

    .settings-tab-list.list-inline {
        margin: 0;
        border-bottom: 1px solid $wp-border-color;

        .list-inline-item {
            padding: 0;
            margin: 0;

            a {
                display: block;
                padding: 8px;
                margin-bottom: -1px;
                font-weight: 700;
                color: $wp-black;
                text-decoration: none;
                border-bottom: 1px solid $wp-border-color;
                opacity: 0.6;

                @include transition;

                &:hover {
                    border-bottom-color: $wp-black;
                    opacity: 1;
                }

                &:focus,
                &:active {
                    border-bottom-color: $wp-blue;
                    outline: 0;
                    box-shadow: none;
                    opacity: 1;
                }
            }

            &.active a {
                color: $wp-blue;
                border-bottom-color: $wp-blue;
                opacity: 1;
            }
        }
    }
</style>
