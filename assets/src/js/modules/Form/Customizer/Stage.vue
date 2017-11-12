<template>
    <div id="customizer-stage">
        <div :id="`wemail-form-${form.id}`" :class="formClassNames" :style="template.style">
            <ul class="no-margin">
                <li
                    v-for="(field, index) in template.fields"
                    :key="field.id"
                    :data-index="index"
                    data-source="stage"
                    class="wemail-form-field clearfix"
                >
                    <component
                        :is="`field-${field.type}`"
                        :field="field"
                        :form="form"
                    ></component>

                    <div class="control-buttons">
                        <p>
                            <i class="fa fa-arrows move"></i>
                            <i class="fa fa-pencil" @click="editField(field, index)"></i>
                            <i
                                :class="['fa fa-trash-o', field.type === 'email' ? 'disabled' : '']"
                                @click="deleteField(index)"
                            ></i>
                        </p>
                    </div>
                </li>

                <li class="button-container">
                    <button
                        type="button"
                        class="button button-primary"
                        v-text="template.submitButton.label"
                    ></button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import FieldEmail from './fields/email.vue';
    import FieldFullName from './fields/fullName.vue';
    import FieldFirstName from './fields/firstName.vue';
    import FieldLastName from './fields/lastName.vue';
    import FieldHeader from './fields/header.vue';
    import FieldHtml from './fields/html.vue';

    export default {
        components: {
            FieldEmail,
            FieldFullName,
            FieldFirstName,
            FieldLastName,
            FieldHeader,
            FieldHtml
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

            fieldSettings: {
                type: Object,
                required: true
            }
        },

        computed: {
            template() {
                return this.form.template;
            },

            formClassNames() {
                const classNames = ['wemail-form'];

                classNames.push(`label-position-${this.template.label.position}`);
                classNames.push(`label-align-${this.template.label.align}`);
                classNames.push(`submit-position-${this.template.submitButton.position}`);
                classNames.push(`submit-size-${this.template.submitButton.size}`);

                return classNames;
            }
        },

        mounted() {
            const vm = this;

            $('#customizer-stage .wemail-form').sortable({
                placeholder: 'wemail-form-field-dropzone',
                items: '.wemail-form-field',
                handle: '.control-buttons .move',
                scroll: true,

                sort() {
                    $('body').addClass('wemail-form-field-is-dragging');
                },

                stop() {
                    $('body').removeClass('wemail-form-field-is-dragging');
                },

                update(e, ui) {
                    const item = ui.item[0];
                    const data = item.dataset;
                    const source = data.source;
                    const toIndex = parseInt($(ui.item).index(), 10);

                    // add content
                    if (source === 'sidebar') {
                        const fieldType = data.fieldType;
                        const fieldSettings = $.extend(true, {}, vm.fieldSettings[fieldType]);

                        fieldSettings.id = moment().unix();

                        vm.form.template.fields.splice(toIndex, 0, fieldSettings);

                        $('#customizer-stage').find('.ui-draggable, li.button').remove();

                    // move content
                    } else {
                        const fromIndex = parseInt(data.index, 10);
                        const field = vm.form.template.fields.splice(fromIndex, 1);

                        vm.form.template.fields.splice(toIndex, 0, field[0]);
                    }
                }
            });
        },

        methods: {
            editField(field, index) {
                weMail.event.$emit('open-wemail-form-field-settings', index);
            },

            deleteField(index) {
                const vm = this;

                vm.warn({
                    text: vm.i18n.deleteWarnMsg,
                    confirmButtonText: vm.i18n.yesDeleteIt,
                    cancelButtonText: vm.i18n.noCancelIt
                }).then((deleteIt) => {
                    if (deleteIt) {
                        vm.form.template.fields.splice(index, 1);
                    }
                });
            }
        }
    };
</script>

<style lang="scss">
    #customizer-stage {
        position: relative;
        width: 100%;
        padding: 25px;
        overflow-x: hidden;
        overflow-y: auto;
        background-color: #666;
        box-shadow: inset 0 0 20px 1px rgba(53, 53, 53, 0.87);

        .wemail-form-field-dropzone {
            height: 50px;
            background: fade-out($wp-blue, 0.8);
            border: 1px dashed $wp-blue;
        }

        .ui-sortable-helper {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .button-container + .ui-sortable-helper + .wemail-form-field-dropzone {
            display: none;
        }
    }

    body.wemail-form-field-is-dragging {

        .control-buttons {
            display: none !important;
        }
    }

    .wemail-form {
        padding: 20px;
        margin: 0;
        background-color: #fff;
        box-shadow: 0 0 20px 1px rgba(53, 53, 53, 0.87);

        * {
            margin-top: 0;
        }

        .wemail-form-field {
            position: relative;
            padding: 10px;
            margin: 0;
            overflow: hidden;

            &:hover {

                .control-buttons {
                    display: block;
                }
            }

            .control-buttons {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 10;
                display: none;
                width: 100%;
                height: 100%;
                margin: 0;
                text-align: center;
                background: fade-out($wp-blue, 0.8);
                border: 1px dashed $wp-blue;

                p {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    margin: -14px 0 0 -60px;
                    font-size: 12px;
                    line-height: 1;
                    color: #eee;
                    background-color: $wp-black;
                }

                i {
                    padding: 8px;
                    cursor: pointer;

                    &.move {
                        cursor: move;
                    }

                    &.disabled {
                        color: #222;
                        pointer-events: none;
                        cursor: default;
                        background-color: #444;

                        i:hover {
                            background-color: $wp-black;
                        }
                    }
                }

                i:hover {
                    background-color: $wp-blue;
                }
            }
        }

        .wemail-form-required-field {
            color: $wp-red;
        }

        .button-container {
            padding: 10px;
        }

        label {
            font-weight: 700;
        }

        &.label-position-top {

            label {
                display: block;
                margin-bottom: 2px;
            }

            input {
                display: block;
                width: 100%;
            }
        }

        &.label-position-left {

            label {
                float: left;
                width: 25%;
                padding-right: 10px;
                margin-bottom: 0;
            }

            input {
                float: left;
                width: 75%;
            }
        }

        &.label-position-right {

            label {
                float: right;
                width: 25%;
                padding-left: 10px;
                margin-bottom: 0;
            }

            input {
                float: left;
                width: 75%;
            }
        }

        &.label-position-hidden {

            label {
                display: none;
            }

            input {
                width: 100%;
            }
        }

        &.label-align-left {

            label {
                text-align: left;
            }
        }

        &.label-align-right {

            label {
                text-align: right;
            }
        }

        &.submit-position-left {

            .button-container {
                text-align: left;
            }
        }

        &.submit-position-center {

            .button-container {
                text-align: center;
            }
        }

        &.submit-position-right {

            .button-container {
                text-align: right;
            }
        }

        &.submit-size-auto {

            .button-container .button {
                width: auto;
            }
        }

        &.submit-size-block {

            .button-container .button {
                width: 100%;
            }
        }
    }
</style>
