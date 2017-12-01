<template>
    <table class="form-table">
        <tbody>
            <tr>
                <th>{{ __('Campaign Name') }}</th>
                <td>
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="block" v-model="campaign.name">
                            <p class="hint">{{ __('Enter a name to help you remember what this campaign is all about. Only you will see this.') }}</p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>{{ __('Campaign Type') }}</th>
                <td>
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <label><input type="radio" value="standard" v-model="campaign.type"> {{ __('Standard') }}</label>
                        </li>
                        <li class="list-inline-item">
                            <label><input type="radio" value="automatic" v-model="campaign.type"> {{ __('Automatic') }}</label>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr v-if="campaign.type === 'standard'">
                <th>{{ __('Subscribers') }}</th>
                <td>
                    <div class="row">
                        <div class="col-6">
                            <p class="clearfix">
                                <strong>{{ __('Lists') }}</strong>
                            </p>

                            <lists-dropdown v-model="campaign.lists" class="margin-bottom-20"></lists-dropdown>

                            <p class="clearfix">
                                <strong>{{ __('Segments') }}</strong>
                            </p>

                            <multiselect
                                v-model="selectedSegments"
                                :options="segments"
                                :multiple="true"
                                :close-on-select="false"
                                :preserve-search="true"
                                :placeholder="__('Select Segments')"
                                label="name"
                                track-by="name"
                                :custom-label="customLabel"
                            >
                                <template slot="option" slot-scope="props">{{ props.option.name }}</template>
                            </multiselect>
                        </div>
                    </div>
                </td>
            </tr>
            <tr v-else>
                <th>{{ __('Automatically Send') }}</th>
                <td>
                    <div class="row">
                        <div class="col-4">
                            <select v-model="campaign.event.action" class="form-control">
                                <option v-for="event in events" :value="event.action">{{ event.actionTitle }}</option>
                            </select>
                        </div>

                        <div v-if="eventOptions.length" class="col-4">
                            <select v-model="campaign.event.value" class="form-control">
                                <option v-for="option in eventOptions" :value="option.id">
                                    {{ option.name }}
                                </option>
                            </select>
                        </div>

                        <div v-if="eventOptions.length" class="col-4 automatic-schedule">
                            <input v-if="campaign.event.schedule_type !== 'immediately'" class="small-text" type="number" min="1" v-model="campaign.event.schedule_offset">
                            <select v-model="campaign.event.schedule_type">
                                <option value="immediately">{{ __('Immediately') }}</option>
                                <option value="hour">{{ __('hour(s) after') }}</option>
                                <option value="day">{{ __('day(s) after') }}</option>
                                <option value="week">{{ __('week(s) after') }}</option>
                            </select>
                        </div>

                        <div v-if="!eventOptions.length" class="col-8">
                            <p><em>{{ __('no option found for this action') }}</em></p>
                        </div>
                    </div>
                </td>
            </tr>
            <slot></slot>
        </tbody>
    </table>
</template>

<script>
    export default {
        props: {
            namespace: {
                type: String,
                required: true
            }
        },

        computed: {
            lists() {
                return this.$store.state[this.namespace].lists;
            },

            segments() {
                return this.$store.state[this.namespace].segments;
            },

            campaign() {
                return this.$store.state[this.namespace].campaign;
            },

            eventTypes() {
                return this.$store.state[this.namespace].eventTypes;
            },

            events() {
                return this.$store.state[this.namespace].events;
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
                return _.chain(this.events)
                    .filter({
                        action: this.campaign.event.action
                    })
                    .head()
                    .value()
                    .options;
            }
        },

        created() {
            if (!this.campaign.event.value) {
                this.setFirstEventOption(this.campaign.event.action);
            }
        },

        watch: {
            'campaign.event.action': 'setFirstEventOption'
        },

        methods: {
            customLabel(option) {
                return _.truncate(option.name, {
                    length: 22
                });
            },

            customOption(name) {
                return _.truncate(name, {
                    length: 20
                });
            },

            setFirstEventOption(action) {
                if (this.eventOptions.length) {
                    this.campaign.event.value = this.eventOptions[0].id;
                }
            }
        }
    };
</script>
