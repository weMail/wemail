<template>
    <div class="sidebar-content" id="customizer-tab-fields">
        <ul class="clearfix">
            <li
                v-for="field in formFields"
                :key="field"
                :class="buttonClass[field]"
                :data-field-type="field"
                data-source="sidebar"
                @click="addField(field)"
            >{{ i18n[field] }}</li>
        </ul>
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

        computed: {
            buttonClass() {
                const classNames = {};

                this.formFields.forEach((field) => {
                    classNames[field] = ['button'];
                });

                this.form.template.fields.forEach((field) => {
                    if (['email', 'html'].indexOf(field.type) < 0) {
                        classNames[field.type].push('disabled');
                    }
                });

                return classNames;
            }
        },

        mounted() {
            $('#customizer-tab-fields .button').draggable({
                connectToSortable: '#customizer-stage .wemail-form',
                helper: 'clone',
                revert: 'invalid',
                cancel: '.disabled',

                drag() {
                    $('body').addClass('wemail-form-field-is-dragging');
                },

                stop() {
                    $('body').removeClass('wemail-form-field-is-dragging');
                }
            }).disableSelection();
        },

        methods: {
            addField(fieldType) {
                console.log(fieldType);
            }
        }
    };
</script>

<style lang="scss">
    #customizer-tab-fields {

        ul {
            display: block;
            margin: 0;

            li.button {
                display: block;
                float: left;
                width: 48%;
                margin: 0 0 10px;
                text-align: left;

                &:nth-child(odd) {
                    margin-right: 2%;
                }
            }
        }
    }
</style>
