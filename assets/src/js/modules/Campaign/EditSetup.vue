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
            <tr v-else>
                <th>{{ i18n.automaticallySend }}</th>
                <td>
                    <div class="row">
                        <div class="col-4">
                            <select v-model="campaign.event.action" class="form-control">
                                <option v-for="event in events" :value="event.action">{{ event.actionTitle }}</option>
                            </select>
                        </div>

                        <div v-if="eventOptions.length" class="col-4">
                            <select v-model="campaign.event.option_value" class="form-control">
                                <option v-for="option in eventOptions" :value="option.id">
                                    {{ option.name }}
                                </option>
                            </select>
                        </div>

                        <div v-if="eventOptions.length" class="col-4 automatic-schedule">
                            <input v-if="campaign.event.schedule_type !== 'immediately'" class="small-text" type="number" min="1" v-model="campaign.event.schedule_offset">
                            <select v-model="campaign.event.schedule_type">
                                <option value="immediately">{{ i18n.immediately }}</option>
                                <option value="hour">{{ i18n.hoursAfter }}</option>
                                <option value="day">{{ i18n.daysAfter }}</option>
                                <option value="week">{{ i18n.weeksAfter }}</option>
                            </select>
                        </div>

                        <div v-if="!eventOptions.length" class="col-8">
                            <p><em>{{ i18n.noOptionFoundForAction }}</em></p>
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
            ...weMail.Vuex.mapState('campaignEdit', [
                'i18n', 'lists', 'segments', 'campaign', 'eventTypes', 'events'
            ]),

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
            },

            eventOptions() {
                return weMail._.chain(this.events)
                    .filter({
                        action: this.campaign.event.action
                    })
                    .head()
                    .value()
                    .options;
            }
        },

        watch: {
            'campaign.event.action': 'onChangeEventAction'
        },

        methods: {
            customLabel(option) {
                return weMail._.truncate(option.name, {
                    length: 22
                });
            },

            customOption(name) {
                return weMail._.truncate(name, {
                    length: 20
                });
            },

            onChangeEventAction(action) {
                if (this.eventOptions.length) {
                    this.campaign.event.option_value = this.eventOptions[0].id;
                }
            }
        }
    };
</script>
