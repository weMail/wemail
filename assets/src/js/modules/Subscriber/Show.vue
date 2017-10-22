<template>
    <div v-if="isLoaded" class="wemail-single-subscriber">
        <div v-if="subscriber" class="row">
            <div class="col-md-5">
                <div class="wemail-box-plain">
                    <div class="row">
                        <div class="col-3 line-height-0">
                            <img class="profile-image" :src="dummyImageURL" alt="">
                        </div>
                        <div class="col-9 no-left-padding">
                            <div class="profile-summery">
                                <h1 class="subscriber-name">{{ subscriberName }}</h1>

                                <a class="subscriber-email" :href="`mailto:${subscriber.email}`">{{ subscriber.email }}</a>

                                <ul v-if="networks.length" class="subscriber-networks list-inline">
                                    <li v-for="network in networks" class="list-inline-item">
                                        <a :href="network.link" v-html="network.icon" target="_blank"></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wemail-box-plain">
                    <h4 class="box-title">
                        {{ i18n.informations }}

                        <a
                            href="#"
                            class="button button-link float-right"
                            @click.prevent="editInfo"
                        >{{ i18n.editInfo }}</a>
                    </h4>

                    <ul class="list-two-columns">
                        <li>
                            <label>{{ i18n.lists }}</label>
                            <div>
                                <span v-for="list in inLists" class="badge-tag">
                                    <i :class="['fa', list.classNames]"></i> {{ list.name }}
                                </span>
                            </div>
                        </li>
                        <li>
                            <label>{{ i18n.address1 }}</label>
                            <div>-</div>
                        </li>
                        <li>
                            <label>{{ i18n.address2 }}</label>
                            <div>-</div>
                        </li>
                        <li>
                            <label>{{ i18n.city }}</label>
                            <div>-</div>
                        </li>
                        <li>
                            <label>{{ i18n.state }}</label>
                            <div>-</div>
                        </li>
                        <li>
                            <label>{{ i18n.country }}</label>
                            <div>-</div>
                        </li>
                        <li>
                            <label>{{ i18n.zip }}</label>
                            <div>-</div>
                        </li>
                        <li>
                            <label>{{ i18n.dob }}</label>
                            <div>-</div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-7">
                activity list
            </div>
        </div>

        <div v-else class="error"><p>{{ i18n.subscriberNotFound }}</p></div>
    </div>
</template>

<script>
    export default {
        routeName: 'subscriberShow',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('subscriberShow', ['i18n', 'subscriber', 'lists', 'dummyImageURL', 'socialNetworks']),

            meta() {
                return this.subscriber.meta;
            },

            subscriberName() {
                if (!this.subscriber.first_name) {
                    return this.i18n.noName;
                }

                const name = [this.subscriber.first_name, this.subscriber.last_name];

                return name.join(' ');
            },

            networks() {
                const vm = this;
                const networks = [];

                if (!vm.subscriber.meta.social) {
                    vm.subscriber.meta.social = {};
                }

                this.socialNetworks.networks.forEach((network) => {
                    if (vm.meta.social[network]) {
                        networks.push({
                            name: network,
                            title: vm.i18n[network],
                            link: vm.meta.social[network],
                            icon: vm.socialNetworks.icons[network]
                        });
                    }
                });

                return networks;
            },

            inLists() {
                const vm = this;

                const lists = this.subscriber.lists.map((listItem) => {
                    const list = _.find(vm.lists, {
                        id: listItem.id
                    });

                    listItem.name = list.name;

                    switch (listItem.status) {
                        case 'subscribed':
                            listItem.classNames = 'fa-check-circle text-success';
                            break;

                        case 'unsubscribed':
                            listItem.classNames = 'fa-times-circle text-danger';
                            break;

                        case 'unconfirmed':
                            listItem.classNames = 'fa-exclamation-circle text-warning';
                            break;

                        default:
                            listItem.classNames = '';
                            break;
                    }

                    return listItem;
                });

                const subscribed = lists.filter((list) => {
                    return list.status === 'subscribed';
                });

                const unsubscribed = lists.filter((list) => {
                    return list.status === 'unsubscribed';
                });

                const unconfirmed = lists.filter((list) => {
                    return list.status === 'unconfirmed';
                });

                return subscribed.concat(unconfirmed).concat(unsubscribed);
            }
        },

        methods: {
            editInfo() {
                console.log('editInfo');
            }
        }
    };
</script>

<style lang="scss">
    .wemail-single-subscriber {
        padding-top: 10px;

        .profile-image {
            max-width: 100%;
            border: 1px solid $wp-border-color-darken;
        }

        .profile-summery {

            .subscriber-name {
                padding: 0;
                margin-bottom: 5px;
                line-height: 1.4;
            }

            .subscriber-email {
                text-decoration: none;
            }

            .subscriber-networks {

                a {
                    font-size: 18px;
                    color: $wp-black;
                }
            }
        }

        .badge-tag {
            max-width: 140px;

            @include text-truncate;
        }
    }
</style>
