<template>
    <div v-if="!isLoaded">
        <h1>{{ i18n.createCampaign }}</h1>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>{{ i18n.campaignName }}</th>
                    <td>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" class="block" v-model="campaign.name">
                                <p class="hint">{{ i18n.campaignNameHint }}</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.campaignType }}</th>
                    <td>
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <label><input type="radio" value="standard" v-model="campaign.type"> {{ i18n.standard }}</label>
                            </li>
                            <li class="list-inline-item">
                                <label><input type="radio" value="automatic" v-model="campaign.type"> {{ i18n.automatic }}</label>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr v-if="campaign.type === 'standard'">
                    <th>{{ i18n.subscribers }}</th>
                    <td>
                        <div class="row">
                            <div class="col-6">
                                <p class="clearfix">
                                    <strong>{{ i18n.lists }}</strong>
                                </p>
                                <multiselect
                                    v-model="campaign.lists"
                                    :options="lists"
                                    :multiple="true"
                                    :close-on-select="false"
                                    :clear-on-select="false"
                                    :hide-selected="true"
                                    :preserve-search="true"
                                    :placeholder="i18n.selectLists"
                                    label="name"
                                    track-by="name"
                                    :custom-label="customLabel"
                                >
                                    <template slot="option" scope="props">{{ props.option.name }}</template>
                                </multiselect>

                                <hr>

                                <p class="clearfix">
                                    <strong>{{ i18n.segments }}</strong>
                                </p>
                                <multiselect
                                    v-model="campaign.segments"
                                    :options="segments"
                                    :multiple="true"
                                    :close-on-select="false"
                                    :clear-on-select="false"
                                    :hide-selected="true"
                                    :preserve-search="true"
                                    :placeholder="i18n.selectSegments"
                                    label="name"
                                    track-by="name"
                                    :custom-label="customLabel"
                                >
                                    <template slot="option" scope="props">{{ props.option.name }}</template>
                                </multiselect>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button class="button button-primary" @click="create">{{ i18n.createCampaign }}</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <progress-bar
            :i18n="i18n"
            :steps="steps"
            :current-step="currentStep"
            scope="create"
        ></progress-bar>
    </div>
</template>

<script>
    import ProgressBar from './templates/ProgressBar.vue';

    export default {
        routeName: 'campaignCreate',

        mixins: weMail.getMixins('routeComponent'),

        components: {
            ProgressBar
        },

        data() {
            return {
                currentStep: 'setup',
                campaign: {
                    name: '',
                    type: 'standard',
                    lists: [],
                    segments: []
                }
            };
        },

        computed: {
            ...weMail.Vuex.mapState('campaignCreate', ['i18n', 'steps', 'lists', 'segments'])
        },

        methods: {
            customLabel(option) {
                return weMail._.truncate(option.name, {
                    length: 15
                });
            },

            customOption(name) {
                return weMail._.truncate(name, {
                    length: 20
                });
            },

            create() {
                const vm = this;

                weMail.api.post('/campaigns', {
                    data: {
                        name: this.campaign.name,
                        type: this.campaign.type,
                        lists: this.campaign.lists.map((list) => {
                            return list.id;
                        }),
                        segments: this.campaign.segments.map((segment) => {
                            return segment.id;
                        })
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
