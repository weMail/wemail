<template>
    <div id="customizer-sidebar">
        <div v-if="currentTab !== 'fieldSettings'" class="sidebar-header">
            <div class="sidebar-header-main">
                <a
                    v-for="tab in headerButtons"
                    href="#"
                    :class="[tab === currentTab ? 'active' : '']"
                    @click.prevent="currentTab = tab"
                >{{ i18n[tab] }}</a>
            </div>
        </div>

        <!-- .sidebar-content -->
        <component
            v-for="tab in tabs"
            :is="componentName"
            :key="tab"
            v-show="currentTab === tab"
            :i18n="i18n"
            :form="form"
            :form-fields="formFields"
            :index="openFieldSettingsIndex"
            @save-settings="currentTab = 'fields'"
        ></component>
    </div>
</template>

<script>
    import TabFields from './TabFields.vue';
    import TabTemplateStyle from './TabTemplateStyle.vue';
    import TabSettings from './TabSettings.vue';
    import TabFieldSettings from './TabFieldSettings.vue';

    export default {
        components: {
            TabFields,
            TabTemplateStyle,
            TabSettings,
            TabFieldSettings
        },

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
            }
        },

        data() {
            return {
                tabs: ['fields', 'templateStyle', 'settings', 'fieldSettings'],
                currentTab: 'fields',
                headerButtons: ['fields', 'templateStyle', 'settings'],
                openFieldSettingsIndex: 0
            };
        },

        computed: {
            componentName() {
                return `tab-${_.kebabCase(this.currentTab)}`;
            }
        },

        created() {
            weMail.event.$on('open-wemail-form-field-settings', this.openFieldSettings);
        },

        methods: {
            openFieldSettings(index) {
                this.currentTab = 'fieldSettings';
                this.openFieldSettingsIndex = index;
            }
        }
    };
</script>

<style lang="scss">
    #customizer-sidebar {
        position: relative;
        min-width: 350px;
        max-width: 350px;
        background-color: #fff;
        border-left: 1px solid $wp-input-border-color;
    }

    .sidebar-header {
        height: 50px;
        text-align: center;

        * {
            margin: 0;
        }

        .button-group {
            margin-top: 15px;

            .button {
                padding: 0 25px;
            }
        }


        h3 {
            position: relative;
            font-size: 1.5em;
            font-weight: 200;

            &.field-settings-title {
                line-height: 2.6;
            }
        }
    }

    .sidebar-content {
        padding: 15px;
    }

    .customizer-sidebar-settings-fields {
        position: absolute;
        top: 50px;
        left: 0;
        width: 100%;
        height: calc(100% - 60px - 34px);
        overflow: auto;

        &.no-save-button {
            height: calc(100% - 60px);
        }
    }

    .wemail-form-settings-save-button.button.button-link {
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

    .control-property {

        &:first-child .property-title {
            border-top: 0;
        }
    }

    .property-title {
        padding: 10px 15px;
        margin: 0;
        background-color: $wp-body-bg;
        border-top: 1px solid $wp-border-color;
        border-bottom: 1px solid $wp-border-color;
    }

    .property {
        padding: 10px 15px;
    }

    .property-value {
        float: right;
    }

    .control-section-title {
        padding: 8px 14px;
        margin-bottom: 0;
        font-size: 18px;
        font-weight: 300;
    }

    .sidebar-header-main {
        display: flex;

        a {
            flex: 1;
            height: 49px;
            font-weight: 500;
            line-height: 3.8;
            color: #777;
            text-decoration: none;
            background-color: $wp-border-color;
            border-right: 1px solid $wp-border-color-darken;
            border-bottom: 1px solid $wp-border-color-darken;

            &.active {
                color: $wp-font-color;
                background-color: #fff;
                border-bottom: 0;
            }

            &:focus {
                box-shadow: none;
            }

            &:last-child {
                border-right: 0;
            }
        }
    }
</style>
