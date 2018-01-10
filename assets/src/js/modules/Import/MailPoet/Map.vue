<template>
    <div>
        <div v-if="!settings.map.length">
            <p class="text-center">{{ __('Fetching MailPoet Merge Fields') }}...</p>
        </div>

        <div v-else class="row">
            <div class="col-md-2"></div>

            <div class="col-md-8">
                <table class="wp-list-table widefat fixed striped valign-top margin-bottom-20">
                    <thead>
                        <tr>
                            <th>{{ __('MailPoet field') }}</th>
                            <th>{{ __('weMail meta field') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="map in settings.map">
                            <td>
                                <strong>{{ getMetaField(map.mailpoet) }}</strong>
                            </td>

                            <td>
                                <select v-model="map.wemail">
                                    <option value="">{{ __('Ignore this field') }}</option>
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
                mailPoetFields: [],
                wemailMeta: []
            };
        },

        created() {
            const vm = this;

            if (!vm.settings.mailpoet_list_id) {
                return vm.$router.push({
                    name: 'importMailPoetList'
                });
            }

            weMail.api
                .import()
                .mailpoet('v2')
                .meta_fields()
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
                                        mailpoet: field.name,
                                        wemail: (field.name === 'email') ? 'email' : ''
                                    });
                                });

                                vm.mailPoetFields = fieldResponse.fields;
                                vm.wemailMeta = metaResponse.meta;
                            }
                        });
                });

            return true;
        },

        methods: {
            getMetaField(name) {
                return _.find(this.mailPoetFields, {
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
                    name: 'importMailPoetProgress'
                });
            }
        }
    };
</script>
