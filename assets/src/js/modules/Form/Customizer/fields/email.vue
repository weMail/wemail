<template>
    <div
        class="wemail-form-field-email"
        :style="field.style"
    >
        <label
            :for="`wemail-form-field-${field.id}`"
        >
            {{ field.label }}
            <span v-if="field.required" class="wemail-form-required-field">*</span>
        </label>

        <input
            type="email"
            :id="`wemail-form-field-${field.id}`"
            :placeholder="field.placeholder"
        >
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
