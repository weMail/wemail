<template>
    <div>
        <div v-if="!settings.map.length">
            <p class="text-center">{{ __('Fetching MailChimp Merge Fields') }}...</p>
        </div>

        <div v-else class="row">
            <div class="col-md-2"></div>

            <div class="col-md-8">
                <table class="wp-list-table widefat fixed striped valign-top margin-bottom-20">
                    <thead>
                        <tr>
                            <th class="mailchimp-fields">{{ __('MailChimp *|MERGE|* field') }}</th>
                            <th class="mailchimp-fields">{{ __('weMail meta field') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="map in settings.map">
                            <td>
                                <strong>{{ getMergeField(map.mailchimp) }}</strong>
                            </td>

                            <td>
                                <select v-model="map.wemail">
                                    <option value="">{{ __('Select a weMail field') }}</option>
                                    <option
                                        v-for="wemailField in wemailMeta"
                                        :value="wemailField.name"
                                        :disabled="isOptionDisabled(wemailField.name)"
                                    >{{ wemailField.title }}</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button
                    type="button"
                    class="button button-primary"
                    @click="goToNext"
                >{{ __('Start Importing') }}</button>
            </div>

            <div class="col-md-2"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            settings: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                mailChimpFields: [],
                wemailMeta: []
            };
        },

        created() {
            const vm = this;

            if (!vm.settings.mailchimp_list_id) {
                return vm.$router.push({
                    name: 'importMailChimpSettings'
                });
            }

            weMail.api
                .import()
                .mailchimp()
                .lists(vm.settings.mailchimp_list_id)
                .merge_fields()
                .get()
                .done((fieldResponse) => {
                    weMail.api
                        .subscribers()
                        .meta()
                        .get()
                        .done((metaResponse) => {
                            if (fieldResponse && fieldResponse.fields && fieldResponse.fields.length) {
                                fieldResponse.fields.forEach((field) => {
                                    vm.settings.map.push({
                                        mailchimp: field.name,
                                        wemail: (field.name === 'EMAIL') ? 'email' : ''
                                    });
                                });

                                vm.mailChimpFields = fieldResponse.fields;
                                vm.wemailMeta = metaResponse.meta;
                            }
                        });
                });

            return true;
        },

        methods: {
            getMergeField(name) {
                return _.find(this.mailChimpFields, {
                    name
                }).title;
            },

            isOptionDisabled(name) {
                const isSelected = _.find(this.settings.map, {
                    wemail: name
                });

                return Boolean(isSelected);
            },

            goToNext() {
                this.$router.push({
                    name: 'importMailChimpProgress'
                });
            }
        }
    };
</script>
