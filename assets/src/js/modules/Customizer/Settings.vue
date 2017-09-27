<template>
    <div id="customizer-settings">
        <template>
            <div v-show="currentWindow !== 'content-settings'" class="customizer-settings-header">
                <div class="button-group">
                    <button
                        type="button"
                        :class="['button', currentWindow === 'contentTypes' ? 'active' : '']"
                        @click="currentWindow = 'contentTypes'"
                    >{{ i18n.content }}</button>

                    <button
                        type="button"
                        :class="['button', currentWindow === 'design' ? 'active' : '']"
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
                page/header etc
                <div v-for="section in template.sections">
                    <h4>{{ section.name }}</h4>
                    <pre v-for="content in section.contents">{{ content.type }}</pre>
                </div>
            </div>
        </template>

        <template>
            <div v-if="currentWindow === 'content-settings'" class="customizer-settings-header content-settings">
                <h3>{{ i18n[contentType] }}</h3>

                <div class="button-group">
                    <button
                        type="button"
                        :class="['button', settingsTab === 'content' ? 'active' : '']"
                        @click="settingsTab = 'content'"
                    >{{ i18n.content }}</button>
                    <button
                        type="button"
                        :class="['button', settingsTab === 'style' ? 'active' : '']"
                        @click="settingsTab = 'style'"
                    >{{ i18n.style }}</button>
                    <button
                        type="button"
                        :class="['button', settingsTab === 'settings' ? 'active' : '']"
                        @click="settingsTab = 'settings'"
                    >{{ i18n.settings }}</button>
                </div>
            </div>

            <div v-if="currentWindow === 'content-settings'" class="content-settings-controls">
                <component
                    :is="getSettingsComponentName()"
                    :current-tab="settingsTab"
                    :i18n="i18n"
                    :section-index="sectionIndex"
                    :content-index="contentIndex"
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
                contentType: '',
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
                this.currentWindow = 'content-settings';
                this.contentType = this.template.sections[sectionIndex].contents[contentIndex].type;
                this.settingsTab = 'content';
                this.sectionIndex = sectionIndex;
                this.contentIndex = contentIndex;
            },

            getSettingsComponentName() {
                const contentType = weMail._.kebabCase(this.contentType);

                return `customizer-content-settings-${contentType}`;
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
</style>
