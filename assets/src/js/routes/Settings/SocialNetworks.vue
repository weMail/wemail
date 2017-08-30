<template>
    <div class="row">
        <div class="col-6">
            <label v-for="setting in settings">
                <strong>{{ i18n[setting.name] }}</strong>
                <input type="text" v-model="setting.url">
            </label>
        </div>
    </div>
</template>

<script>
    import SettingsMixin from './SettingsMixin.js';

    export default {
        routeName: 'socialNetworks',

        mixins: [SettingsMixin]
    };
</script>
