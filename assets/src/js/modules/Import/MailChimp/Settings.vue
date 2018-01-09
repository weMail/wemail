<template>
    <div class="row import-settings">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <form @submit.prevent="saveSettings">
                <fieldset :disabled="saving">
                    <h3>{{ __('MailChimp API Key') }}</h3>
                    <input type="text" class="import-input" v-model="mailChimpSettings.key" :placeholder="__('MailChimp API Key')">
                    <button
                        type="submit"
                        class="button button-primary import-button"
                    >{{ __('Start') }}</button>
                </fieldset>
            </form>
        </div>
        <div class="col-md-2"></div>
    </div>
</template>

<script>
    export default {
        props: {
            settings: {
                type: Object,
                required: false
            }
        },

        data() {
            return {
                saving: false,
                mailChimpSettings: {
                    key: ''
                }
            };
        },

        created() {
            const vm = this;

            weMail.api.settings('mailchimp').get().done((response) => {
                if (response && response.settings && response.settings.key) {
                    vm.mailChimpSettings.key = response.settings.key;
                }
            });
        },

        methods: {
            saveSettings() {
                const vm = this;

                vm.saving = true;

                weMail.api
                    .import()
                    .mailchimp()
                    .ping(vm.mailChimpSettings.key)
                    .post()
                    .done((response) => {
                        vm.$router.push({
                            name: 'importMailChimpList'
                        });
                    })
                    .fail((jqXHR) => {
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            vm.error(jqXHR.responseJSON.message);
                        }
                    })
                    .always(() => {
                        vm.saving = false;
                    });
            }
        }
    };
</script>

<style lang="scss">
    .wemail {

        .import-settings {

            h3 {
                margin-bottom: 3px;
            }

            input.import-input {
                width: 100%;
                height: 40px;
                padding-right: 90px;
                margin-right: -80px;
                font-size: 18px;
                border-right: 0;
            }
        }
    }

    .wp-core-ui {

        .button {

            &.button-primary.import-button {
                height: 39px;
                padding-right: 22px;
                padding-left: 22px;
                font-size: 13px;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }
        }
    }
</style>
