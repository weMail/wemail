<template>
    <div>
        form.vue <code>{{ id }}</code><br>
        <router-link to="/forms" class="button" tag="button">Back</router-link>
    </div>
</template>

<script>
    export default {
        computed: {
            id() {
                return this.$route.params.id;
            }
        }
    };
</script>
