<template>
    <div>
        <div class="control-property">
            <h4 class="property-title">{{ i18n.label }}</h4>

            <div class="property">
                <input
                    type="text"
                    class="form-control"
                    v-model="field.label"
                >
            </div>
        </div>

        <div class="control-property">
            <h4 class="property-title">{{ i18n.placeholder }}</h4>

            <div class="property">
                <input
                    type="text"
                    class="form-control"
                    v-model="field.placeholder"
                >
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            i18n: {
                type: Object,
                required: true
            },

            field: {
                type: Object,
                required: true
            }
        }
    };
</script>
