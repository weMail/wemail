<template>
    <div>
        <h1>{{ __('Import: MailChimp') }}</h1>

        <template v-if="!isReady">
            <p class="text-center">{{ __('Preparing Importer') }}...</p>
        </template>

        <template v-else>
            <progress-bar :routes="routes"></progress-bar>

            <router-view :settings="settings"></router-view>
        </template>
    </div>
</template>

<script>
    import ProgressBar from '../components/ProgressBar.vue';

    export default {
        components: {
            ProgressBar
        },

        data() {
            return {
                isReady: false,
                routes: [
                    {
                        name: 'importMailChimpSettings',
                        title: __('Settings')
                    },
                    {
                        name: 'importMailChimpList',
                        title: __('Select List')
                    },
                    {
                        name: 'importMailChimpMap',
                        title: __('Map Fields')
                    },
                    {
                        name: 'importMailChimpProgress',
                        title: __('Progress')
                    }
                ],
                settings: {
                    mailchimp_list_id: '',
                    wemail_list_id: '',
                    import_confirmed_only: false,
                    import_mailchimp_list: true,
                    overwite_existing_subscriber: true,
                    life_stage: '',
                    map: []
                }
            };
        },

        created() {
            const vm = this;

            weMail.api.import().get().done((response) => {
                if (response && response.settings && response.settings.type === 'mailchimp') {
                    vm.settings = response.settings;
                    vm.$router.push({
                        name: 'importMailChimpProgress'
                    });
                }

            }).always(() => {
                vm.isReady = true;
            });
        }
    };
</script>
