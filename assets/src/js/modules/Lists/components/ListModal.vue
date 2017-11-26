<template>
    <div class="wemail-modal wemail-modal-new-list">
        <div class="wemail-modal-dialog">
            <div class="wemail-modal-content">
                <form @submit.prevent="saveForm">
                    <fieldset :disabled="isDisabled">
                        <div class="wemail-modal-header">
                            <h5 class="wemail-modal-title">{{ i18n.addNewList }}</h5>
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
                                    v-model="list.name"
                                    @input="isInvalidName = false"
                                >
                            </label>

                            <label class="d-block margin-bottom-20">
                                <strong>{{ i18n.description }}</strong>
                                <textarea rows="5" class="form-control" v-model="list.description"></textarea>
                            </label>

                            <label class="d-block margin-bottom-20">
                                <input type="checkbox" v-model="isPrivate"> {{ i18n.makeItPrivate }}

                                <small class="d-block">{{ i18n.whatIsPrivateMsg }}</small>
                            </label>
                        </div>
                        <div class="wemail-modal-footer">
                            <button
                                type="button"
                                class="button button-link"
                                data-dismiss="wemail-modal"
                            >{{ __('Cancel') }}</button>&nbsp;&nbsp;

                            <button
                                type="submit"
                                class="button button-primary"
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
            i18n: {
                type: Object,
                required: true
            },

            list: {
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
                isInvalidName: false
            };
        },

        computed: {
            isPrivate: {
                get() {
                    return (this.list.type === 'private') || false;
                },

                set(isPrivate) {
                    this.list.type = isPrivate ? 'private' : 'public';
                }
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
                    name: 'listsIndex'
                });
            },

            saveForm() {
                const vm = this;

                let api;

                if (vm.isEmpty(vm.list.name)) {
                    vm.isInvalidName = true;
                    return;
                }

                vm.isDisabled = true;

                $(vm.$el).on('hide.wemail.modal', (e) => {
                    return e.preventDefault();
                });

                if (!vm.edit) {
                    api = weMail.api.lists().create(vm.list);
                } else {

                    api = weMail.api.lists(vm.$route.params.id).update(vm.list);
                }

                api.done((response) => {
                    this.$store.commit('global/updateLists', {
                        id: response.id,
                        name: response.name,
                        type: response.type
                    });

                    vm.isDisabled = false;
                    $(vm.$el).off('hide.wemail.modal').wemailModal('hide');
                });
            }
        }
    };
</script>
