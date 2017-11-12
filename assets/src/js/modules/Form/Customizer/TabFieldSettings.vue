<template>
    <div id="customizer-tab-field-settings">
        <div class="sidebar-header">
            <h3 class="field-settings-title">{{ i18n[field.type] }}</h3>
        </div>

        <div class="customizer-sidebar-settings-fields">
            <component
                :is="componentName"
                :i18n="i18n"
                :field="field"
            ></component>
        </div>

        <button
            type="button"
            class="button button-link wemail-form-settings-save-button"
            @click="closeSettings"
        >{{ i18n.close }}</button>
    </div>
</template>

<script>
    import SettingsEmail from './settings/email.vue';
    import SettingsFullName from './settings/fullName.vue';
    import SettingsFirstName from './settings/firstName.vue';
    import SettingsLastName from './settings/lastName.vue';
    import SettingsHeader from './settings/header.vue';
    import SettingsHtml from './settings/html.vue';

    export default {
        components: {
            SettingsEmail,
            SettingsFullName,
            SettingsFirstName,
            SettingsLastName,
            SettingsHeader,
            SettingsHtml
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
            },

            index: {
                type: Number,
                required: true
            }
        },

        computed: {
            field() {
                return this.form.template.fields[this.index];
            },

            componentName() {
                return `settings-${_.kebabCase(this.field.type)}`;
            }
        },

        methods: {
            closeSettings() {
                this.$emit('save-settings');
            }
        }
    };
</script>

<style lang="scss">
    #customizer-tab-field-settings {
        position: relative;
        height: 100%;

        .sidebar-header {
            border-bottom: 1px solid $wp-border-color;
        }
    }
</style>
