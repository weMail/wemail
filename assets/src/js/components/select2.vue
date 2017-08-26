<template>
    <select :value="value" @change="updateValue($event.target.value)">
        <slot></slot>
        <option v-for="(val, key) in data" :value="key">{{ val }}</option>
    </select>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: {}
            },

            value: {
                type: [String, Number]
            }
        },

        methods: {
            updateValue(value) {
                console.log(value);
                this.$emit('input', value);
                this.$emit('change', value);
            }
        }
    };
</script>
