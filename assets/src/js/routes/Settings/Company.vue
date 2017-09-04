<template>
    <div class="row">
        <div class="col-sm-6">
            <label>
                <strong>{{ i18n.companyName }}</strong>
                <input type="text" v-model="settings.name">
            </label>

            <label>
                <strong>{{ i18n.address1 }}</strong>
                <input type="text" v-model="settings.address1">
            </label>

            <label>
                <strong>{{ i18n.address2 }}</strong>
                <input type="text" v-model="settings.address2">
            </label>

            <label>
                <strong>{{ i18n.city }}</strong>
                <input type="text" v-model="settings.city">
            </label>

            <div v-if="hasCountryStates" class="multiselect-container">
                <strong>{{ i18n.state }}</strong>
                <multiselect
                    v-model="stateSelected"
                    :options="stateOptions"
                    key="code"
                    label="name"
                    :placeholder="i18n.selectState"
                >
                    <span slot="noResult">{{ i18n.noStateFound }}</span>
                </multiselect>
            </div>

            <div class="multiselect-container">
                <strong>{{ i18n.country }}</strong>
                <multiselect
                    v-model="countrySelected"
                    :options="countryOptions"
                    key="code"
                    label="name"
                    :placeholder="i18n.selectCountry"
                    :maxHeight="200"
                >
                    <span slot="noResult">{{ i18n.noCountryFound }}</span>
                </multiselect>
            </div>

            <label>
                <strong>{{ i18n.zip }}</strong>
                <input type="text" v-model="settings.zip">
            </label>

            <label>
                <strong>{{ i18n.phone }}</strong>
                <input type="text" v-model="settings.phone">
            </label>

            <label>
                <strong>{{ i18n.mobile }}</strong>
                <input type="text" v-model="settings.mobile">
            </label>

            <label>
                <strong>{{ i18n.website }}</strong>
                <input type="text" v-model="settings.website">
            </label>
        </div>

        <div class="col-sm-6">
            <div class="text-center company-logo no-logo">
                <button
                    type="button"
                    class="button button-primary button-hero"
                    @click="addComapanyLogo"
                >{{ i18n.addComapanyLogo }}</button>
            </div>
        </div>
    </div>
</template>

<script>
    import SettingsMixin from './SettingsMixin.js';

    export default {
        routeName: 'company',

        mutations: {
            updateCountryStates(state, payload) {
                state.states = payload;
            }
        },

        mixins: [SettingsMixin],

        computed: {
            ...weMail.Vuex.mapState('company', ['countries', 'states']),

            countryOptions() {
                return weMail._.map(this.countries, (name, code) => {
                    return {
                        code,
                        name
                    };
                });
            },

            countrySelected: {
                get() {
                    return {
                        code: this.settings.country,
                        name: this.countries[this.settings.country]
                    };
                },

                set(country) {
                    this.settings.country = country.code;
                }
            },

            stateOptions() {
                return weMail._.map(this.states, (name, code) => {
                    return {
                        code,
                        name
                    };
                });
            },

            stateSelected: {
                get() {
                    return {
                        code: this.settings.state,
                        name: this.states[this.settings.state]
                    };
                },

                set(state) {
                    this.settings.state = state.code;
                }
            },

            hasCountryStates() {
                return Object.keys(this.states).length;
            }
        },

        watch: {
            'settings.country'(country) {
                const vm = this;

                vm.$root.showLoadingAnime = true;

                weMail.ajax.get('get_country_states', {
                    country
                }).done((response) => {
                    const states = response.data.states;
                    const names = Object.keys(states);

                    if (names.length) {
                        vm.$store.commit('company/updateCountryStates', states);
                        vm.settings.state = names[0];
                    } else {
                        vm.$store.commit('company/updateCountryStates', {});
                        vm.settings.state = null;
                    }

                }).always(() => {
                    vm.$root.showLoadingAnime = false;
                });
            }
        },

        methods: {
            addComapanyLogo(e) {
                console.log(e);
            }
        }
    };
</script>

<style lang="scss" scoped>
    .company-logo {

        &.no-logo {
            padding: 90px 0;
            border: 2px dashed $wp-border-color-darken;
        }
    }
</style>
