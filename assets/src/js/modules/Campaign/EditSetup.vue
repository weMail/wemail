<template>
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
                                v-model="selectedLists"
                                :options="lists"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="i18n.selectLists"
                                label="name"
                                track-by="name"
                                :custom-label="customLabel"
                                class="margin-bottom-20"
                            >
                                <template slot="option" scope="props">{{ props.option.name }}</template>
                            </multiselect>

                            <p class="clearfix">
                                <strong>{{ i18n.segments }}</strong>
                            </p>
                            <multiselect
                                v-model="selectedSegments"
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
        </tbody>
    </table>
</template>

<script>
    export default {
        data() {
            return {
                currentStep: 'setup'
            };
        },

        computed: {
            ...weMail.Vuex.mapState('campaignEdit', ['i18n', 'lists', 'segments', 'campaign']),

            selectedLists: {
                get() {
                    const vm = this;

                    return this.lists.filter((list) => {
                        return vm.campaign.lists.indexOf(list.id) >= 0;
                    });
                },

                set(lists) {
                    this.campaign.lists = lists.map((list) => {
                        return list.id;
                    });
                }
            },

            selectedSegments: {
                get() {
                    const vm = this;

                    return this.segments.filter((segment) => {
                        return vm.campaign.segments.indexOf(segment.id) >= 0;
                    });
                },

                set(segments) {
                    this.campaign.segments = segments.map((segment) => {
                        return segment.id;
                    });
                }
            }
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
            }
        }
    };
</script>
