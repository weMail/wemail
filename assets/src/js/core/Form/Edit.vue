<template>
    <div v-if="isLoaded" id="wemail-form-customizer" class="wemail-form-edit">
        <div class="clearfix">
            <inline-editor
                v-model="form.name"
                :container-class="['pull-left']"
                :top="12"
            >
                <h1 class="customizer-title">
                    {{ form.name }}
                </h1>
            </inline-editor>

            <div class="pull-right top-right-buttons">
                <button
                    class="button button-primary"
                    @click="saveForm"
                >{{ i18n.saveForm }}</button>
            </div>
        </div>

        <div id="customizer-container" class="d-flex">
            <stage
                :i18n="i18n"
                :form="form"
                :field-settings="fieldSettings"
            ></stage>

            <sidebar
                :i18n="i18n"
                :form="form"
                :form-fields="formFields"
            ></sidebar>
        </div>
    </div>
</template>

<script>
    import Stage from './Customizer/Stage.vue';
    import Sidebar from './Customizer/Sidebar.vue';

    export default {
        routeName: 'formEdit',

        components: {
            Stage,
            Sidebar
        },

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('formEdit', ['i18n', 'form', 'formFields', 'fieldSettings'])
        },

        created() {
            $('body').addClass('wemail-fixed-body');
        },

        destroyed() {
            $('body').removeClass('wemail-fixed-body');
        },

        methods: {
            saveForm() {
                weMail.api.forms(this.form.id).update(this.form);
            }
        }
    };
</script>

<style lang="scss">
    #wemail-form-customizer {
        position: fixed;
        top: 42px;
        left: 0;
        width: calc(100% - 202px);
        margin-left: 182px;

        .customizer-title {
            margin-bottom: 8px;
        }
    }

    #customizer-container {
        height: calc(100vh - 110px);
        background-color: #fff;
        border: 1px solid $wp-border-color-darken;
    }
</style>
