<template>
    <div v-if="isLoaded">
        <div class="margin-bottom-10">
            <h1>{{ __('Campaign') }}: {{ campaign.name }}</h1>
        </div>

        <div class="row margin-bottom-20">
            <div class="col-md-6">
                <div class="postbox campaign-summery">
                    <h3 class="title">{{ __('Summery') }}</h3>
                    <table class="wp-list-table widefat fixed striped valign-top">
                        <tbody>
                            <tr>
                                <th>{{ __('Status') }}</th>
                                <td>
                                    <div class="campaign-email-status" v-html="campaignStatus(campaign)"></div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Subject') }}</th>
                                <td>{{ campaign.email.subject }}</td>
                            </tr>

                            <tr v-if="campaign.type === 'standard'">
                                <th>{{ __('Lists') }}</th>
                                <td>{{ campaignLists }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('From') }}</th>
                                <td>{{ campaign.email.sender_name }} &lt;{{ campaign.email.sender_email }}&gt;</td>
                            </tr>
                            <tr>
                                <th>{{ __('Reply To') }}</th>
                                <td>{{ campaign.email.reply_to_name }} &lt;{{ campaign.email.reply_to_email }}&gt;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="postbox campaign-chart">
                    <h3 class="title">{{ __('Email Stats') }}</h3>
                    <div id="campaign-email-stats" style="width: 100%; height: 250px;"></div>
                </div>
            </div>
        </div>

        <div class="margin-bottom-20">
            <h3>{{ __('Link Statistics') }}</h3>
            <table class="wp-list-table widefat fixed striped valign-top">
                <thead>
                    <th class="link-stat-num">#</th>
                    <th class="link-stat-url">{{ __('Links') }}</th>
                    <th>{{ __('Unique Clicks') }}</th>
                    <th>{{ __('Total Clicks') }}</th>
                </thead>
                <tbody>
                    <tr v-if="!linkStats.length">
                        <td colspan="4" class="text-center">{{ __('No link statistic found for this campaign') }}</td>
                    </tr>

                    <template v-else>
                        <tr v-for="(url, index) in linkStats">
                            <td>{{ index + 1 }}</td>
                            <td>{{ url.url }}</td>
                            <td>{{ url.uniqueClicks }}</td>
                            <td>{{ url.totalClicks }}</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <hr>

        <subscribers :campaign="campaign"></subscribers>
    </div>
</template>

<script>
    import '../../../vendor/flot/jquery.flot.js';
    import '../../../vendor/flot/jquery.flot.pie.js';

    import campaignStatus from './mixins/campaignStatus.js';
    import Subscribers from './components/Subscribers.vue';

    export default {
        routeName: 'campaignShow',

        mutations: {
            updateSubscribers(state, payload) {
                state.subscribers = payload;
            }
        },

        mixins: [
            ...weMail.getMixins('routeComponent', 'helpers'),
            campaignStatus
        ],

        components: {
            Subscribers
        },

        data() {
            return {
                campaignLinks: null,
                filterMenu: [

                ]
            };
        },

        computed: {
            ...Vuex.mapState('campaignShow', ['campaign']),

            campaignLists() {
                const lists = [];

                this.campaign.lists.forEach((listId) => {
                    const list = _.find(weMail.lists, {
                        id: listId
                    });

                    if (list) {
                        lists.push(list.name);
                    }
                });

                return lists.length ? lists.join(', ') : '&mdash;';
            },

            linkStats() {
                const vm = this;

                const stats = [];

                if (vm.campaignLinks) {
                    _(vm.campaignLinks).forEach((campaignLink) => {
                        const url = {
                            id: campaignLink.id,
                            url: campaignLink.url,
                            uniqueClicks: 0,
                            totalClicks: 0
                        };

                        if (Object.keys(campaignLink.subscribers).length) {
                            _(campaignLink.subscribers).forEach((subscriber) => {
                                url.uniqueClicks++;

                                subscriber.clicked_at.forEach(() => {
                                    url.totalClicks++;
                                });
                            });
                        }

                        stats.push(url);
                    });
                }

                return stats;
            }
        },

        methods: {
            registeredStoreModule() {
                if (!this.campaign) {
                    this.$router.push({
                        name: 'campaign404'
                    });

                    return false;
                }

                if (this.campaign.status === 'active' || this.campaign.status === 'completed') {
                    return true;
                }

                this.$router.push({
                    name: 'campaignEdit',
                    params: {
                        id: this.campaign.id
                    }
                });

                return false;
            },

            afterLoaded() {
                const vm = this;

                weMail.api.campaigns(this.campaign.id).link_statistics().get().done((response) => {
                    if (response.data) {
                        vm.campaignLinks = response.data;
                    }
                });

                vm.plotEmailStats();
            },

            plotEmailStats() {
                const emailStats = [];

                emailStats.push({
                    label: sprintf('<strong>%s</strong> Clicked', this.campaign.clicked),
                    color: '#8BC34A',
                    data: this.campaign.clicked
                });

                const notClicked = this.campaign.opened - this.campaign.clicked;

                if (notClicked) {
                    emailStats.push({
                        label: sprintf('<strong>%s</strong> Opened but did not click', notClicked),
                        color: '#1EAAF1',
                        data: notClicked
                    });
                }

                const notOpened = this.campaign.sent - this.campaign.opened;

                if (notOpened) {
                    emailStats.push({
                        label: sprintf('<strong>%s</strong> Not opened', notOpened),
                        color: '#00BFA5',
                        data: notOpened
                    });
                }

                if (this.campaign.bounced) {
                    emailStats.push({
                        label: sprintf('<strong>%s</strong> Bounced', this.campaign.bounced),
                        color: '#FD8A6A',
                        data: this.campaign.bounced
                    });
                }

                if (this.campaign.on_queue) {
                    emailStats.push({
                        label: sprintf('<strong>%s</strong> On Queue', this.campaign.on_queue),
                        color: '#F5BE3B',
                        data: this.campaign.on_queue
                    });
                }

                // email stats chart
                $.plot('#campaign-email-stats', emailStats, {
                    series: {
                        pie: {
                            show: true,
                            innerRadius: 0.3,
                            label: {
                                show: true,
                                radius: 0.5,
                                formatter: (label, series) => {
                                    return `<div style="font-size:8pt; text-align:center; padding:2px; color:white;"">${Math.round(series.percent)}%</div>`;
                                },
                                threshold: 0.05
                            }
                        }
                    },
                    legend: {
                        show: true
                    }
                });
            }
        }
    };
</script>

<style lang="scss">
    .campaign-summery {

        table {
            height: 250px;
            border: 0;

            th {
                width: 130px;
                font-weight: 500;
            }

            th,
            td {
                vertical-align: middle;
            }
        }
    }

    .link-stat-num {
        width: 45px;
    }

    #campaign-email-stats {

        .legend {

            table {
                margin: 25px 25px 0 0 !important;

                .legendColorBox {
                    padding-bottom: 6px;

                    & > div {

                        & > div {
                            border-width: 6px !important;
                        }
                    }
                }

                .legendLabel {
                    padding-bottom: 6px;
                    font-size: 12px;

                    strong {
                        font-size: 17px;
                    }
                }
            }
        }
    }
</style>
