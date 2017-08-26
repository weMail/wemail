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
                <strong>{{ i18n.address2 }} <small>({{ i18n.optional }})</small></strong>
                <input type="text" v-model="settings.address2">
            </label>

            <label>
                <strong>{{ i18n.city }}</strong>
                <input type="text" v-model="settings.city">
            </label>

            <label v-if="hasCountryStates">
                <strong>{{ i18n.state }}</strong>
                <select2 :data="states" v-model="settings.state"></select2>
            </label>

            <label>
                <strong>{{ i18n.country }}</strong>
                <select2 :data="countries" v-model="settings.country"></select2>
            </label>

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
            image
            <pre>{{ settings }}</pre>
            <!-- <pre>{{ countries }}</pre> -->
            <pre>{{ states }}</pre>
        </div>
    </div>
</template>

<script>
    import settingsMixin from './settings-mixin.js';

    export default {
        mixins: [settingsMixin],

        computed: {
            ...weMail.Vuex.mapState(['countries', 'states']),

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
                        vm.$store.commit('updateCountryStates', states);
                        vm.settings.state = names[0];
                    } else {
                        vm.$store.commit('updateCountryStates', {});
                        vm.settings.state = null;
                    }
                }).always(() => {
                    vm.$root.showLoadingAnime = false;
                });

            }
        }
    };
</script>
