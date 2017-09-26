<template>
    <div v-if="isLoaded" class="row">
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
            <div v-if="!logo" class="text-center company-logo no-logo">
                <button
                    type="button"
                    class="button button-link"
                    @click="addComapanyLogo"
                >{{ i18n.addComapanyLogo }}</button>
            </div>
            <div v-else class="text-center company-logo">
                <img :src="logo" alt="Comapany Logo"><br>
                <button
                    type="button"
                    class="button"
                    @click="addComapanyLogo"
                >{{ i18n.changeLogo }}</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'settingsCompany',

        mutations: {
            updateCountryStates(state, payload) {
                state.states = payload;
            },

            updateLogo(state, payload) {
                state.logo = payload;
            }
        },

        mixins: weMail.getMixins('settings', 'routeComponent'),

        data() {
            return {
                fileFrame: null
            };
        },

        computed: {
            ...weMail.Vuex.mapState('settingsCompany', ['countries', 'states']),

            logo: {
                get() {
                    return this.$store.state.settingsCompany.logo;
                },

                set(logo) {
                    this.$store.commit('settingsCompany/updateLogo', logo);
                }
            },

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

                // TODO: @loading

                weMail.ajax.get('get_country_states', {
                    country
                }).done((response) => {
                    const states = response.data.states;
                    const names = Object.keys(states);

                    if (names.length) {
                        vm.$store.commit('settingsCompany/updateCountryStates', states);
                        vm.settings.state = names[0];
                    } else {
                        vm.$store.commit('settingsCompany/updateCountryStates', {});
                        vm.settings.state = null;
                    }
                });
            }
        },

        methods: {
            addComapanyLogo(e) {
                e.preventDefault();

                const vm = this;
                const selectedFile = {
                    id: 0,
                    url: '',
                    type: ''
                };

                if (vm.fileFrame) {
                    vm.fileFrame.open();
                    return;
                }

                const fileStates = [
                    new wp.media.controller.Library({
                        library: wp.media.query(),
                        multiple: false,
                        title: vm.i18n.selectLogo,
                        priority: 20,
                        filterable: 'uploaded'
                    })
                ];

                vm.fileFrame = wp.media({
                    title: vm.i18n.selectLogo,
                    library: {
                        type: ''
                    },
                    button: {
                        text: vm.i18n.selectLogo
                    },
                    multiple: false,
                    states: fileStates
                });

                vm.fileFrame.on('select', () => {
                    const selection = vm.fileFrame.state().get('selection');

                    selection.map((attachment) => {
                        attachment = attachment.toJSON();

                        if (attachment.id) {
                            selectedFile.id = attachment.id;
                        }

                        if (attachment.url) {
                            selectedFile.url = attachment.url;
                        }

                        if (attachment.type) {
                            selectedFile.type = attachment.type;
                        }

                        vm.onSelectLogo(selectedFile);

                        return null;
                    });
                });

                vm.fileFrame.on('ready', () => {
                    vm.fileFrame.uploader.options.uploader.params = {
                        type: 'wemail-company-logo',
                        test: 5
                    };
                });

                vm.fileFrame.open();
            },

            onSelectLogo(attachment) {
                if (!attachment.id || (attachment.type !== 'image')) {
                    this.alert({
                        type: 'error',
                        text: this.i18n.pleaseSelectAnImage
                    });

                    return;
                }

                this.logo = attachment.url;
                this.settings.imageId = attachment.id;
            }
        }
    };
</script>

<style lang="scss">
    .company-logo {
        width: 250px;

        &.no-logo {
            padding: 90px 0;
            border: 1px dashed $wp-border-color-darken;
        }

        img {
            max-width: 100%;
            height: auto;
        }
    }
</style>
