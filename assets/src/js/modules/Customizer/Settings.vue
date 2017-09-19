<template>
    <div id="customizer-settings">
        <div class="customizer-settings-header">
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
    </div>
</template>

<script>
    export default {
        props: {
            i18n: {
                type: Object,
                required: true
            },

            contentTypes: {
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
                currentWindow: 'contentTypes'
            };
        },

        methods: {
            getContentTypeBtnStyle(type) {
                return {
                    backgroundImage: `url(${this.contentTypes.settings[type].image})`
                };
            }
        }
    };
</script>

<style lang="scss">
    #customizer-settings {
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
</style>
