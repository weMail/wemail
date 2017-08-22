<template>
    <div>
        <label>
            Social Networks<br>
            <input type="text" v-model="settings.name">
        </label>
    </div>
</template>

<script>
    import settingsMixin from './settings-mixin.js';

    export default {
        mixins: [settingsMixin]
    };
</script>
