<template>
    <div>
        <template v-if="!lists.length">
            <p class="text-center">{{ __('Fetching MailPoet lists') }}...</p>
        </template>

        <template v-else>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>{{ __('Select MailPoet list') }}</th>
                        <td>
                            <ul>
                                <li v-for="list in lists" class="margin-bottom-10">
                                    <label>
                                        <input type="radio" :value="list.id" v-model="settings.mailpoet_list_id">  {{ list.name }} <small class="text-muted">({{ list.count }})</small>
                                    </label>
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <th>{{ __('Import MailPoet list') }}</th>
                        <td>
                            <ul>
                                <li class="margin-bottom-10">
                                    <label>
                                        <input type="radio" :value="true" v-model="settings.import_mailpoet_list"> {{ __('Import subscribers with MailPoet list') }}
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="radio" :value="false" v-model="settings.import_mailpoet_list"> {{ __('Import subscribers into weMail list') }}
                                    </label>
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr v-if="!settings.import_mailpoet_list">
                        <th>{{ __('Select weMail list') }}</th>
                        <td>
                            <select v-model="settings.wemail_list_id">
                                <option value="">{{ __('Select weMail list') }}</option>
                                <option
                                    v-for="wemailList in wemailLists"
                                    :value="wemailList.id"
                                >{{ wemailList.name }}</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>{{ __('Overwite existing subscriber') }}</th>
                        <td>
                            <label>
                                <input type="checkbox" v-model="settings.overwite_existing_subscriber"> {{ __('Yes') }}
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th>{{ __('Life Stage') }}</th>
                        <td>
                            <select v-model="settings.life_stage">
                                <option
                                    v-for="lifeStage in lifeStages"
                                    :value="lifeStage.name"
                                >{{ lifeStage.title }}</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button
                type="button"
                class="button button-primary"
                @click="goToNext"
            >{{ __('Next') }}</button>
        </template>
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
                lists: []
            };
        },

        computed: {
            lifeStages() {
                const names = this.$store.state.global.lifeStages.names;
                const i18n = this.$store.state.global.lifeStages.i18n;

                return names.map((name) => {
                    return {
                        name,
                        title: i18n[name]
                    };
                });
            },

            wemailLists() {
                return this.$store.state.global.lists;
            }
        },

        created() {
            const vm = this;

            if (!vm.settings.life_stage) {
                vm.settings.life_stage = vm.$store.state.global.lifeStages.default;
            }

            weMail.api.import().mailpoet('v2').lists().get().done((response) => {
                vm.lists = response.lists;
            });
        },

        methods: {
            goToNext() {
                if (!this.settings.mailpoet_list_id) {
                    return this.error(__('You must select a MailPoet list'));
                }

                return this.$router.push({
                    name: 'importMailPoetMap'
                });
            }
        }
    };
</script>

<style lang="scss" scoped>
    .form-table {

        th {
            width: 300px;
            padding-top: 15px;
        }

        ul {
            margin: 0;
        }
    }
</style>
