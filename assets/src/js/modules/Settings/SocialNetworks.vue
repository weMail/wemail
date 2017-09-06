<template>
    <div class="row">
        <div class="col-6">
            <label v-for="network in order">
                <strong>{{ i18n[network] }}</strong>
                <input type="text" v-model="settings[network]">
            </label>
        </div>
    </div>
</template>

<script>
    import SettingsMixin from './SettingsMixin.js';

    export default {
        routeName: 'socialNetworks',

        mixins: [SettingsMixin],

        computed: {
            order() {
                return this.$store.state.socialNetworks.order;
            }
        }
    };
</script>
