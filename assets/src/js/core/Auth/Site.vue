<template>
    <div v-if="isLoaded">
        <button
            type="button"
            class="button button-primary button-hero"
            @click="authSite"
            :disabled="isLoading"
        >{{ __('Connect weMail') }}</button>
    </div>
</template>

<script>
    export default {
        routeName: 'authSite',

        mixins: weMail.getMixins('routeComponent'),

        data() {
            return {
                isLoading: false
            };
        },

        created() {
            if (weMail.user.hash) {
                this.$router.push({
                    name: 'overview'
                });
            }
        },

        methods: {
            authSite() {
                const vm = this;

                vm.isLoading = true;

                weMail.ajax.post('auth_site').done((response) => {
                    window.location.reload(true);
                }).fail((jqXHR) => {
                    if (jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
                        vm.error({
                            html: jqXHR.responseJSON.data.message
                        });
                    }

                    vm.isLoading = false;
                });
            }
        }
    };
</script>
