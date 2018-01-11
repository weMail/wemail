<template>
    <div
        class="wemail-form-field-html"
        :style="field.style"
        v-html="field.html"
    ></div>
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
