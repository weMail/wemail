<template>
    <div>
        <div class="customizer-sidebar-settings-fields no-save-button">
            <div class="control-property">
                <h4 class="property-title">
                    {{ i18n.onSubmit }}
                </h4>

                <div class="property">
                    <select class="form-control margin-bottom-10" v-model="form.settings.onSubmit">
                        <option value="show_message">{{ i18n.showMessage }}</option>
                        <option value="redirect">{{ i18n.redirectToCustomUrl }}</option>
                    </select>

                    <textarea
                        v-if="form.settings.onSubmit === 'show_message'"
                        rows="5"
                        v-model="form.settings.message"
                        class="form-control"
                    ></textarea>

                    <input
                        v-if="form.settings.onSubmit === 'redirect'"
                        type="text"
                        class="form-control"
                        v-model="form.settings.redirectTo"
                        placeholder="http://..."
                    >
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title">
                    {{ i18n.formAction }}
                </h4>

                <div class="property">
                    <strong class="d-block">{{ i18n.subscribeToList }}</strong>
                    <lists-dropdown v-model="selectedLists"></lists-dropdown>
                </div>
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
            lists() {
                return this.$store.state.global.lists;
            },

            selectedLists: {
                get() {
                    let actionLists = _.find(this.form.settings.actions, {
                        action: 'subscribe_to_list'
                    });

                    if (!actionLists) {
                        this.form.settings.actions.push({
                            action: 'subscribe_to_list',
                            value: []
                        });

                        actionLists = _.find(this.form.settings.actions, {
                            action: 'subscribe_to_list'
                        });
                    }

                    return actionLists.value;
                },

                set(lists) {
                    const actionLists = _.find(this.form.settings.actions, {
                        action: 'subscribe_to_list'
                    });

                    actionLists.value = lists;
                }
            }
        }
    };
</script>
