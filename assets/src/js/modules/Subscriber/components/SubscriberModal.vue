<template>
    <div class="wemail-modal wemail-modal-new-subscriber" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="wemail-modal-dialog" role="document">
            <div class="wemail-modal-content">
                <form @submit.prevent="saveForm">
                    <fieldset :disabled="isDisabled">
                        <div class="wemail-modal-header">
                            <h5 class="wemail-modal-title">{{ modalTitle }}</h5>

                            <button type="button" class="close" data-dismiss="wemail-modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="wemail-modal-body">
                            <label class="d-block margin-bottom-20">
                                <strong>{{ __('Email') }}</strong> <span class="text-danger">*</span>
                                <input
                                    type="email"
                                    :class="['form-control', isInvalidEmail ? 'form-control-danger' : '']"
                                    v-model="subscriber.email"
                                    @input="removeErrors"
                                >

                                <p v-if="isInvalidEmail" class="hint text-danger">{{ __('Invalid email') }}</p>
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ __('First Name') }}</strong>
                                <input type="text" class="form-control" v-model="subscriber.first_name">
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ __('Last Name') }}</strong>
                                <input type="text" class="form-control" v-model="subscriber.last_name">
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ __('Phone') }}</strong>
                                <input type="text" class="form-control" v-model="subscriber.phone">
                            </label>

                            <label class="d-block no-bottom-margin">
                                <strong class="d-block-with-border">{{ __('Lists') }}</strong>
                            </label>

                            <ul>
                                <li v-for="list in lists">
                                    <label>
                                        <input
                                            type="checkbox"
                                            :value="list.id"
                                            v-model="subscriber.lists"
                                        > {{ list.name }}
                                    </label>
                                </li>
                            </ul>

                            <em class="d-block small">
                                <span class="text-danger">*</span> {{ __('Required field') }}
                            </em>

                            <ul v-if="responseErrors.length" class="text-danger">
                                <li>{{ __('Error') }} <hr></li>
                                <li v-for="error in responseErrors">{{ error }}</li>
                            </ul>
                        </div>
                        <div class="wemail-modal-footer">
                            <button
                                type="button"
                                class="button button-link"
                                data-dismiss="wemail-modal"
                            >{{ __('Cancel') }}</button>&nbsp;&nbsp;

                            <span v-if="!edit" class="button-group wemail-dropdown">
                                <button
                                    type="submit"
                                    class="button button-primary button-extra-padding-medium"
                                >{{ __('Save') }}</button>

                                <button
                                    type="button"
                                    class="button button-primary button-menu"
                                    data-toggle="wemail-dropdown"
                                >
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <div class="wemail-dropdown-menu wemail-dropdown-menu-right">
                                    <a class="wemail-dropdown-item" href="#" @click.prevent="saveForm">{{ __('Save') }}</a>
                                    <a class="wemail-dropdown-item" href="#" @click.prevent="saveAndAddAnother">{{ __('Save and add another') }}</a>
                                    <a class="wemail-dropdown-item" href="#" @click.prevent="saveAndGoToDetailsPage">{{ __('Save and go to details page') }}</a>
                                </div>
                            </span>
                            <button
                                v-else
                                type="submit"
                                class="button button-primary button-extra-padding-medium"
                            >{{ __('Save') }}</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mixins: weMail.getMixins('dataValidators'),

        props: {
            subscriber: {
                type: Object,
                required: true
            },

            edit: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                isDisabled: false,
                isInvalidEmail: false,
                responseErrors: []
            };
        },

        computed: {
            modalTitle() {
                return this.edit ? __('Edit Subscriber') : __('Add New Subscriber');
            },

            lists() {
                return weMail.lists;
            }
        },

        mounted() {
            $(this.$el)
                .wemailModal('show')
                .on('hidden.wemail.modal', this.onCloseModal);
        },

        methods: {
            onCloseModal() {
                this.$router.push({
                    name: 'subscriberIndex'
                });
            },

            removeErrors() {
                this.isInvalidEmail = false;
                this.responseErrors = [];
            },

            saveForm(callback) {
                const vm = this;

                let api;

                // validate first
                if (!vm.isEmail(vm.subscriber.email)) {
                    vm.isInvalidEmail = true;

                    return;
                }

                vm.isDisabled = true;

                $(vm.$el).on('hide.wemail.modal', (e) => {
                    return e.preventDefault();
                });

                if (!vm.edit) {
                    api = weMail.api.subscribers().create(vm.subscriber);
                } else {
                    api = weMail.api.subscribers(vm.$route.params.id).update(vm.subscriber);
                }

                api.done((response) => {
                    if (response && response.data.id && response.data.id) {
                        $(vm.$el).off('hide.wemail.modal');

                        if (typeof callback === 'function') {
                            callback(vm, response);
                        } else {
                            $(vm.$el).wemailModal('hide');
                        }
                    }

                }).fail((jqXHR) => {
                    const response = jqXHR.responseJSON;

                    if (response && response.errors) {
                        _.forEach(response.errors, (error) => {
                            vm.responseErrors = vm.responseErrors.concat(error);
                        });
                    }

                    vm.isDisabled = false;
                });
            },

            saveAndAddAnother() {
                this.saveForm((vm) => {
                    vm.reset();
                });
            },

            saveAndGoToDetailsPage() {
                this.saveForm((vm, response) => {
                    $(vm.$el).wemailModal('hide');

                    vm.$router.push({
                        name: 'subscriberShow',
                        params: {
                            id: response.data.id
                        }
                    });
                });
            },

            reset() {
                this.subscriber.email = '';
                this.subscriber.firstName = '';
                this.subscriber.lastName = '';
                this.subscriber.phone = '';
                this.subscriber.lists = [];

                this.isDisabled = false;
                this.isInvalidEmail = false;
            }
        }
    };
</script>

<style lang="scss">
    .wemail-modal-new-subscriber {

        .hint {
            margin: 4px 0 5px;
            font-size: 0.85em;
            font-style: italic;
            color: rgba(35, 40, 45, 0.7);
        }
    }
</style>
