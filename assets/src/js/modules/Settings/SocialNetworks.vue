<template>
    <div v-if="isLoaded" class="row">
        <div class="col-6">
            <label v-for="network in order">
                <strong>{{ i18n[network] }}</strong>
                <input type="text" v-model="settings[network]">
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'settingsSocialNetworks',

        mixins: weMail.getMixins('settings', 'routeComponent'),

        computed: {
            order() {
                return this.$store.state.settingsSocialNetworks.order;
            }
        }
    };
</script>
