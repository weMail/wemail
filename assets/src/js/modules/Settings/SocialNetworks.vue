<template>
    <div v-if="isLoaded" class="row">
        <div class="col-6">
            <label v-for="network in networks">
                <strong>{{ title[network] }}</strong>
                <input type="text" v-model="settings[network]">
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'settingsSocialNetworks',

        mixins: weMail.getMixins('settings'),

        computed: {
            networks() {
                return this.$store.state.settingsSocialNetworks.networks;
            },

            title() {
                return this.$store.state.settingsSocialNetworks.title;
            }
        }
    };
</script>
