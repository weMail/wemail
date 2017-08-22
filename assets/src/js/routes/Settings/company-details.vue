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

            <label>
                <strong>{{ i18n.state }}</strong>
                <input type="text" v-model="settings.state">
            </label>

            <label>
                <strong>{{ i18n.zip }}</strong>
                <input type="text" v-model="settings.zip">
            </label>

            <label>
                <strong>{{ i18n.country }}</strong>
                <input type="text" v-model="settings.country">
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
        </div>

    </div>
</template>

<script>
    import settingsMixin from './settings-mixin.js';

    export default {
        mixins: [settingsMixin]
    };
</script>
