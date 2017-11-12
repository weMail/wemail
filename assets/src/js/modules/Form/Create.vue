<template>
    <div v-if="isLoaded" class="wemail-modal wemail-modal-new-form">
        <div class="wemail-modal-dialog">
            <div class="wemail-modal-content">
                <form @submit.prevent="saveForm">
                    <fieldset :disabled="isDisabled">
                        <div class="wemail-modal-header">
                            <h5 class="wemail-modal-title">{{ i18n.addNewForm }}</h5>
                            <button type="button" class="close" data-dismiss="wemail-modal" :disabled="isDisabled">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="wemail-modal-body">
                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.name }}</strong>
                                <input
                                    type="text"
                                    :class="['form-control', isInvalidName ? 'form-control-danger' : '']"
                                    v-model="form.name"
                                    @input="isInvalidName = false"
                                >
                            </label>

                            <label class="d-block margin-bottom-20 form-templates">
                                <strong>{{ i18n.formStyle }}</strong>
                                <div class="row">
                                    <div v-for="style in formStyles" class="col-sm-3">
                                        <a
                                            href="#"
                                            :class="[(style.name === form.style) ? 'active' : '']"
                                            @click.prevent="form.style = style.name"
                                        >
                                            <span class="style-preview" :style="previewStyle(style.image)">&nbsp;</span>
                                            <span class="style-label">{{ style.label }}</span>
                                            <i class="fa fa-check-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.addToList }}</strong>
                                <multiselect
                                    v-model="selectedLists"
                                    :options="lists"
                                    :multiple="true"
                                    :close-on-select="false"
                                    :preserve-search="true"
                                    :custom-label="customLabel"
                                    track-by="id"
                                    label="name"
                                    :placeholder="i18n.selectLists"
                                >
                                    <span slot="noResult">{{ i18n.noListFound }}</span>
                                </multiselect>
                            </label>
                        </div>
                        <div class="wemail-modal-footer">
                            <button
                                type="button"
                                class="button button-link"
                                data-dismiss="wemail-modal"
                            >{{ i18n.cancel }}</button>&nbsp;&nbsp;

                            <button
                                type="submit"
                                class="button button-primary"
                            >{{ i18n.save }}</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'formCreate',

        mixins: weMail.getMixins('routeComponent', 'dataValidators'),

        data() {
            return {
                formId: null,
                isDisabled: false,
                isInvalidName: false
            };
        },

        computed: {
            ...Vuex.mapState('formCreate', ['i18n', 'form', 'formStyles', 'lists']),

            selectedLists: {
                get() {
                    const selectedLists = [];

                    let i = 0;

                    for (i = 0; i < this.lists.length; i++) {
                        if (this.form.lists.indexOf(this.lists[i].id) >= 0) {
                            selectedLists.push(this.lists[i]);
                        }
                    }

                    return selectedLists;
                },

                set(lists) {
                    this.form.lists = lists.map((list) => {
                        return list.id;
                    });
                }
            }
        },

        methods: {
            afterLoaded() {
                $(this.$el)
                    .wemailModal('show')
                    .on('hidden.wemail.modal', this.onCloseModal);
            },

            onCloseModal() {
                if (this.formId) {
                    this.$router.push({
                        name: 'formEdit',
                        params: {
                            id: this.formId
                        }
                    });
                } else {
                    this.$router.push({
                        name: 'formIndex'
                    });
                }
            },

            saveForm() {
                const vm = this;

                if (vm.isEmpty(vm.form.name)) {
                    vm.isInvalidName = true;
                    return;
                }

                vm.isDisabled = true;

                $(vm.$el).on('hide.wemail.modal', (e) => {
                    return e.preventDefault();
                });

                weMail.api.forms().create(this.form).done((response) => {
                    vm.formId = response.id;
                    vm.isDisabled = false;
                    $(vm.$el).off('hide.wemail.modal').wemailModal('hide');
                });
            },

            previewStyle(image) {
                return {
                    backgroundImage: `url(${weMail.cdn}/${image})`
                };
            },

            customLabel(option) {
                return _.truncate(option.name, {
                    length: 22
                });
            }
        }
    };
</script>

<style lang="scss">
    .wemail-modal-new-form {

        .form-templates {

            & > strong {
                display: block;
                padding-bottom: 2px;
                margin-bottom: 5px;
                border-bottom: 1px solid $wp-input-border-color;
            }

            a {
                position: relative;
                display: inline-block;
                color: $wp-font-color;
                text-align: center;
                text-decoration: none;

                &:focus {
                    box-shadow: none;
                }

                &.active {

                    .style-preview {
                        background-position-y: 100px;
                    }

                    .fa {
                        display: inline;
                    }
                }

                .style-preview {
                    display: inline-block;
                    width: 102px;
                    height: 102px;
                    background-size: cover;
                    border: 1px solid #ddd;
                }

                .style-label {
                    display: block;
                }

                .fa {
                    position: absolute;
                    top: -7px;
                    right: -4px;
                    display: none;
                    font-size: 20px;
                    color: $wp-green;
                    background-color: #fff;
                    border-radius: 50%;
                }
            }
        }
    }
</style>
