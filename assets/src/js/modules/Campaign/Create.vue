<template>
    <div v-if="isLoaded">
        <h1>{{ i18n.createCampaign }}</h1>

        <setup namespace="campaignCreate">
            <tr>
                <td></td>
                <td>
                    <button class="button button-primary" @click="create">{{ i18n.createCampaign }}</button>
                </td>
            </tr>
        </setup>

        <progress-bar :i18n="i18n" scope="create"></progress-bar>
    </div>
</template>

<script>
    import ProgressBar from './templates/ProgressBar.vue';
    import Setup from './templates/Setup.vue';

    export default {
        routeName: 'campaignCreate',

        mixins: weMail.getMixins('routeComponent'),

        components: {
            ProgressBar,
            Setup
        },

        computed: {
            ...Vuex.mapState('campaignCreate', ['i18n', 'campaign'])
        },

        methods: {
            create() {
                const vm = this;

                weMail.api.campaigns().create({
                    data: {
                        campaign: this.campaign
                    }
                }).done((response) => {
                    if (response.id) {
                        vm.$router.push({
                            name: 'campaignEditTemplate',
                            params: {
                                id: response.id
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
