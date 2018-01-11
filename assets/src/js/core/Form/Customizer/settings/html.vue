<template>
    <div>
        <div class="control-property">
            <h4 class="property-title">{{ i18n.label }}</h4>

            <div class="property">
                <textarea class="form-control" rows="10" v-model="field.html"></textarea>
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
