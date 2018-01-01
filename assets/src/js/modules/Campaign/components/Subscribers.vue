<template>
    <div>
        <h3>{{ __('Campaign Subscribers') }}</h3>
        <list-table
            name="campaigns-subscribers"
            :i18n="i18n"
            :table-data="subscribers"
            :columns="columns"
            :filter-menu="filterMenu"
        >
            <input slot="search" type="search" v-model="search" :placeholder="__('Search subscriber')">

            <span slot="no-data-found">{{ __('No subscriber found') }}</span>
        </list-table>

        <div class="wemail-modal" id="subscriber-timeline" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="wemail-modal-dialog" role="document">
                <div class="wemail-modal-content">
                    <div class="wemail-modal-header">
                        <h5 class="wemail-modal-title">{{ __('Campaign activity') }}</h5>

                        <button type="button" class="close" data-dismiss="wemail-modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="wemail-modal-body">
                        <div v-if="showTimeline" class="subscriber-timeline">
                            <div class="subscriber-info">
                                <img :src="timeline.image" alt="">
                                <a :href="timeline.name"></a>
                                <router-link :to="timeline.router">{{ timeline.name }}</router-link>
                                <span>{{ timeline.email }}</span>
                            </div>
                            <div class="subscriber-activities">
                                <div v-for="item in timeline.items" class="subscriber-activity">
                                    <!-- <i class="timeline-icon"></i> -->
                                    <i :class="['timeline-icon', 'fa', `fa-${item.icon}`]"></i>
                                    <div class="timeline-item-content">
                                        <p v-html="item.text"></p>
                                        <span class="timeline-time">{{ item.time }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center">
                            {{ __('Loading') }}...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const ListTable = weMail.components.ListTable;

    export default {
        mixins: [
            ...weMail.getMixins('helpers')
        ],

        components: {
            ListTable
        },

        props: {
            campaign: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                search: '',
                apiHandler: {
                    abort() {
                        //
                    }
                },
                columns: [
                    'subscriber',
                    'emailStatus',
                    'lists',
                    'subscriptionStatus'
                ],
                i18n: {
                    subscriber: __('Subscriber'),
                    emailStatus: __('Email Status'),
                    lists: __('Lists'),
                    subscriptionStatus: __('Subscription Status')
                },
                showTimeline: false,
                timeline: {
                    name: '',
                    image: '',
                    email: '',
                    router: {
                        name: 'subscriberShow',
                        params: {
                            id: ''
                        }
                    },
                    items: []
                }
            };
        },

        computed: {
            subscribers() {
                return this.$store.state.campaignShow.subscribers;
            },

            filterMenu() {
                return [
                    {
                        name: 'all',
                        count: this.campaign.sent,
                        title: __('All'),
                        route: {
                            name: 'campaignShow',
                            params: {
                                id: this.campaign.id
                            }
                        }
                    },
                    {
                        name: 'opened',
                        count: this.campaign.opened,
                        title: __('Opened'),
                        route: {
                            name: 'campaignSubscriber',
                            params: {
                                id: this.campaign.id,
                                status: 'opened'
                            }
                        }
                    },
                    {
                        name: 'clicked',
                        count: this.campaign.clicked,
                        title: __('Clicked'),
                        route: {
                            name: 'campaignSubscriber',
                            params: {
                                id: this.campaign.id,
                                status: 'clicked'
                            }
                        }
                    },
                    {
                        name: 'unsubscribed',
                        count: this.campaign.unsubscribed,
                        title: __('Unsubscribed'),
                        route: {
                            name: 'campaignSubscriber',
                            params: {
                                id: this.campaign.id,
                                status: 'unsubscribed'
                            }
                        }
                    },
                    {
                        name: 'bounced',
                        count: this.campaign.bounced,
                        title: __('Bounced'),
                        route: {
                            name: 'campaignSubscriber',
                            params: {
                                status: 'bounced'
                            }
                        }
                    }
                ];
            }
        },

        beforeMount() {
            this.search = this.$router.currentRoute.query.s;
        },

        mounted() {
            const vm = this;

            $('#subscriber-timeline').on('hidden.wemail.modal', () => {
                vm.showTimeline = false;
            });
        },

        watch: {
            '$route.query': 'fetchData',
            search: 'onChangeSearch'
        },

        methods: {
            fetchData() {
                const vm = this;

                vm.apiHandler.abort();

                const query = $.extend(true, {}, vm.$router.currentRoute.query);

                if (vm.$router.currentRoute.params.status) {
                    query.status = vm.$router.currentRoute.params.status;
                }

                vm.apiHandler = weMail
                    .api
                    .campaigns(vm.campaign.id)
                    .subscribers()
                    .query(query)
                    .get()
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('campaignShow/updateSubscribers', response);
                        }
                    });
            },

            onChangeSearch(search) {
                const query = $.extend(true, {}, this.$router.currentRoute.query);

                if (search) {
                    query.s = search;
                } else {
                    Vue.delete(query, 's');
                }

                this.$router.replace({
                    query
                });
            },

            columnSubscriber(subscriber) {
                let name = [subscriber.first_name, subscriber.last_name].join(' ').trim();

                if (!name) {
                    name = `(${__('no name')})`;
                }

                let src = '';

                if (subscriber.image && subscriber.image.sizes && subscriber.image.sizes.thumbnail) {
                    src = weMail.siteURL + subscriber.image.sizes.thumbnail.url;
                } else {
                    src = `https://www.gravatar.com/avatar/${this.md5(subscriber.email)}?d=mm&s=100`;
                }

                return {
                    html: `
                        <div>
                            <img src="${src}">
                            <a class="list-table-title" href="#" @click.prevent="showOpenStatModal('${subscriber.id}')">${name}</a><br>
                            ${subscriber.email}
                        </div>
                    `
                };
            },

            columnEmailStatus(subscriber) {
                let status = __('Sent');

                if (subscriber.clicked) {
                    status = __('Clicked');
                } else if (subscriber.opened) {
                    status = __('Opened');
                }

                return status;
            },

            columnLists(subscriber) {
                const vm = this;

                return subscriber.lists.map((list) => {
                    return _.find(vm.$store.state.global.lists, {
                        id: list.id
                    }).name;
                }).join(', ');
            },

            columnSubscriptionStatus(subscriber) {
                return subscriber.unsubscribed ? __('Unsubscribed') : __('Subscribed');
            },

            showOpenStatModal(subscriberId) {
                const vm = this;

                const subscriber = _.find(this.subscribers.data, {
                    id: subscriberId
                });

                vm.timeline.name = [subscriber.first_name, subscriber.last_name].join(' ').trim();

                let src = '';

                if (subscriber.image && subscriber.image.sizes && subscriber.image.sizes.thumbnail) {
                    src = weMail.siteURL + subscriber.image.sizes.thumbnail.url;
                } else {
                    src = `https://www.gravatar.com/avatar/${vm.md5(subscriber.email)}?d=mm&s=100`;
                }

                vm.timeline.items = [];
                vm.timeline.image = src;
                vm.timeline.email = subscriber.email;
                vm.timeline.router.params.id = subscriber.id;

                $('#subscriber-timeline').wemailModal('show');

                weMail.api.campaigns(vm.campaign.id).subscribers(subscriberId).activities().get().done((response) => {
                    if (response.data) {
                        const items = response.data.map((item) => {
                            const timelineItem = {
                                time: vm.toWPDateTime(item.time, weMail.momentDateTimeFormat)
                            };

                            switch (item.type) {
                                case 'opened':
                                    timelineItem.text = __('Opened');
                                    timelineItem.icon = 'envelope-open';
                                    break;

                                case 'clicked':
                                    timelineItem.text = sprintf('Clicked %s', `<a href="${item.url}">${item.url}</a>`);
                                    timelineItem.icon = 'link';
                                    break;

                                case 'sent':
                                default:
                                    timelineItem.text = __('Email sent');
                                    timelineItem.icon = 'send';
                                    break;
                            }

                            return timelineItem;
                        });

                        vm.timeline.items = items;
                        vm.showTimeline = true;
                    }
                });
            }
        }
    };
</script>

<style lang="scss">
    .wemail-list-table-campaigns-subscribers {

        .column-subscriber {
            width: 300px;

            img {
                float: left;
                width: 40px;
                height: auto;
                margin-right: 8px;
                line-height: 0;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
            }
        }
    }

    .subscriber-info {
        position: relative;
        z-index: 2;

        img {
            float: left;
            width: 40px;
            height: auto;
            margin-right: 8px;
            line-height: 0;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
        }

        a {
            display: block;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }
    }

    .subscriber-activities {
        position: relative;
        z-index: 1;
        padding: 10px 0 0;

        &:before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 15px;
            width: 0;
            content: " ";
            border-right: 1px solid #fff;
            border-left: 1px solid #dcdcdc;
        }

        .subscriber-activity {
            position: relative;
            padding-left: 32px;
            margin-bottom: 15px;

            .timeline-icon {
                position: absolute;
                top: -1px;
                left: 4px;
                display: inline-block;
                width: 22px;
                height: 22px;
                font-size: 12px;
                color: #000;
                background-color: #efefef;
                border: 1px solid #f7f7f7;
                border-radius: 50%;
                box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.3), 0 1px 4px 0 rgba(0, 0, 0, 0.08), 0 3px 1px -2px rgba(0, 0, 0, 0.2);

                &:before {
                    position: absolute;
                    top: 5px;
                    left: 4px;
                }
            }

            .timeline-item-content {

                p {
                    margin-bottom: 4px;
                }
            }

            .timeline-time {
                font-size: 11px;
                opacity: 0.6;
            }
        }
    }
</style>
