<template>
    <div>
        <div class="control-property">
            <h4 class="property-title">{{ i18n.header }}</h4>

            <div class="property">
                <input
                    type="text"
                    class="form-control"
                    v-model="field.text"
                >
            </div>
        </div>
        <div class="control-property">
            <h4 class="property-title">
                {{ i18n.color }}
                <span class="property-value">{{ field.style.color ? field.style.color : '######' }}</span>
            </h4>

            <div class="property">
                <color-picker v-model="field.style.color"></color-picker>
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
