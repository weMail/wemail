<template>
    <div class="wemail-modal wemail-modal-new-subscriber" :id="id" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="wemail-modal-dialog" role="document">
            <div class="wemail-modal-content">
                <div class="wemail-modal-header">
                    <h5 class="wemail-modal-title">{{ i18n.addNewSubscriber }}</h5>
                    <button type="button" class="close" data-dismiss="wemail-modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="wemail-modal-body">
                    <form @submit.prevent="saveForm">
                        <fieldset :disabled="isDisabled">
                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.email }}</strong> <span class="text-danger">*</span>
                                <input
                                    type="email"
                                    :class="['form-control', isInvalidEmail ? 'form-control-danger' : '']"
                                    v-model="subscriber.email"
                                    @input="isInvalidEmail = false"
                                >

                                <p v-if="isInvalidEmail" class="hint text-danger">{{ i18n.invalidEmail }}</p>
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.firstName }}</strong>
                                <input type="text" class="form-control" v-model="subscriber.firstName">
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.lastName }}</strong>
                                <input type="text" class="form-control" v-model="subscriber.lastName">
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.phone }}</strong>
                                <input type="text" class="form-control" v-model="subscriber.phone">
                            </label>

                            <label class="d-block no-bottom-margin">
                                <strong class="d-block-with-border">{{ i18n.lists }}</strong>
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
                        </fieldset>

                        <em class="d-block small">
                            <span class="text-danger">*</span> {{ i18n.requiredField }}
                        </em>
                    </form>
                </div>
                <div class="wemail-modal-footer">
                    <button
                        type="button"
                        class="button button-link"
                        data-dismiss="wemail-modal"
                        :disabled="isDisabled"
                    >{{ __('Cancel') }}</button>&nbsp;&nbsp;

                    <span class="button-group wemail-dropdown">
                        <button
                            type="submit"
                            class="button button-primary button-extra-padding-medium"
                            :disabled="isDisabled"
                            @click="saveForm"
                        >{{ i18n.save }}</button>

                        <button
                            type="button"
                            class="button button-primary button-menu"
                            data-toggle="wemail-dropdown"
                            :disabled="isDisabled"
                        >
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="wemail-dropdown-menu wemail-dropdown-menu-right">
                            <a class="wemail-dropdown-item" href="#" @click.prevent="saveForm">{{ __('Save') }}</a>
                            <a class="wemail-dropdown-item" href="#" @click.prevent="saveAndAddAnother">{{ i18n.saveAndAddAnother }}</a>
                            <a class="wemail-dropdown-item" href="#" @click.prevent="saveAndGoToDetailsPage">{{ i18n.saveAndGoToDetailsPage }}</a>
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mixins: weMail.getMixins('dataValidators', 'helpers'),

        props: {
            scope: {
                type: String,
                required: true
            },

            i18n: {
                type: Object,
                required: true
            },

            lists: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                subscriber: {
                    email: '',
                    firstName: '',
                    lastName: '',
                    phone: '',
                    lists: []
                },
                isDisabled: false,
                isInvalidEmail: false
            };
        },

        computed: {
            id() {
                return `wemail-subscriber-modal-${this._uid}`;
            }
        },

        mounted() {
            weMail.event.$on(`show-new-subscriber-modal-${this.scope}`, this.show);

            $(`#${this.id}`).on('hidden.wemail.modal', this.reset);
        },

        methods: {
            show() {
                $(`#${this.id}`).wemailModal('show');
            },

            saveForm(callback) {
                const vm = this;

                // validate first
                if (!vm.isEmail(vm.subscriber.email)) {
                    vm.isInvalidEmail = true;

                    return;
                }

                vm.isDisabled = true;

                const subscriber = vm.snakeKeys(vm.subscriber);

                weMail.api.subscribers().create(subscriber).done((response) => {
                    if (response && response.data.id && response.data.id) {
                        vm.$emit('subscriber-created', response);

                        if (typeof callback === 'function') {
                            callback(vm, response);
                        } else {
                            vm.close();
                        }
                    }
                });
            },

            saveAndAddAnother() {
                this.saveForm((vm) => {
                    vm.reset();
                });
            },

            saveAndGoToDetailsPage() {
                this.saveForm((vm, response) => {
                    vm.close();

                    vm.$router.push({
                        name: 'subscriberShow',
                        params: {
                            id: response.data.id
                        }
                    });
                });
            },

            close() {
                $(`#${this.id}`).wemailModal('hide');
            },

            reset() {
                this.subscriber = {
                    email: '',
                    firstName: '',
                    lastName: '',
                    phone: '',
                    lists: []
                };

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
