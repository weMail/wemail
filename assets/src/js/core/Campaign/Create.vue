<template>
    <div v-if="isLoaded">
        <h1>{{ __('Create Campaign') }}</h1>

        <setup namespace="campaignCreate">
            <tr>
                <td></td>
                <td>
                    <button class="button button-primary" @click="create">{{ __('Create Campaign') }}</button>
                </td>
            </tr>
        </setup>

        <progress-bar scope="create"></progress-bar>
    </div>
</template>

<script>
    import ProgressBar from './ProgressBar.vue';
    import Setup from './Setup.vue';

    export default {
        routeName: 'campaignCreate',

        mixins: weMail.getMixins('routeComponent'),

        components: {
            ProgressBar,
            Setup
        },

        computed: {
            ...Vuex.mapState('campaignCreate', ['campaign'])
        },

        methods: {
            create() {
                const vm = this;

                weMail.api.campaigns().create(this.campaign).done((response) => {
                    if (response.data && response.data.id) {
                        vm.$router.push({
                            name: 'campaignEditTemplate',
                            params: {
                                id: response.data.id
                            }
                        });
                    }
                });
            }
        }
    };
</script>

<style lang="scss">
</style>
