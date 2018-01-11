<template>
    <div class="wemail-form-field-header">
        <h3 :style="field.style">{{ field.text }}</h3>
    </div>
</template>

<script>
    export default {
        props: {
            field: {
                type: Object,
                required: true
            },

            form: {
                type: Object,
                required: true
            }
        }
    };
</script>
