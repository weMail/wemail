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

                                <div class="subscriber-action wemail-dropdown">
                                    <button
                                        class="button button-link"
                                        type="button"
                                        data-toggle="wemail-dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    ><i class="fa fa-gear"></i></button>
                                    <div class="wemail-dropdown-menu wemail-dropdown-menu-right">
                                        <a
                                            class="wemail-dropdown-item"
                                            href="#delete-subscriber"
                                            @click.prevent="deleteItem"
                                        >{{ i18n.deleteSubscriber }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wemail-box-plain">
                    <h4 class="box-title">
                        {{ i18n.informations }}

                        <a
                            v-if="!isEditingInformations"
                            href="#"
                            class="button button-link float-right"
                            @click.prevent="editInfo"
                        >{{ i18n.editInfo }}</a>
                    </h4>

                    <ul class="list-two-columns">
                        <li>
                            <label>{{ i18n.lists }}</label>
                            <div>
                                <template v-if="!isEditingInformations">
                                    <span v-for="list in inLists" class="badge-tag">
                                        <i :class="['fa', list.classNames]"></i> {{ list.name }}
                                    </span>
                                </template>

                                <template v-else>
                                    <ul>
                                        <li v-for="list in lists">
                                            <label>
                                                <input type="checkbox" :value="list.id" v-model="editLists"> {{ list.name }}
                                                <p class="list-status-info" v-html="showListInfo(list.id)"></p>
                                            </label>
                                        </li>
                                    </ul>
                                </template>
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

                    <hr v-if="isEditingInformations">

                    <p v-if="isEditingInformations" class="text-right">
                        <button
                            type="button"
                            class="button button-link"
                            @click.prevent="canceEditInfo"
                        >{{ i18n.cancel }}</button>&nbsp;&nbsp;

                        <button
                            type="button"
                            class="button button-success button-extra-padding"
                            @click.prevent="saveEditInfo"
                        >{{ i18n.save }}</button>
                    </p>
                </div>
            </div>
            <div class="col-md-7">
                editLists
                <pre>{{ editLists }}</pre>

                subsriber lists
                <pre>{{ subscriber.lists }}</pre>

                informations.lists
                <pre>{{ informations.lists }}</pre>
            </div>
        </div>

        <div v-else class="error"><p>{{ i18n.subscriberNotFound }}</p></div>
    </div>
</template>

<script>
    import deleteSubscriber from './mixins/deleteSubscriber.js';

    export default {
        routeName: 'subscriberShow',

        mixins: [...weMail.getMixins('routeComponent'), deleteSubscriber],

        data() {
            return {
                isEditingInformations: false,
                editLists: [],
                informations: {
                    lists: [],
                    meta: {}
                }
            };
        },

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

                const lists = this.subscriber.lists.map((list) => {
                    list = $.extend(true, {}, list);

                    const weMailList = _.find(vm.lists, {
                        id: list.id
                    });

                    list.name = weMailList.name;

                    switch (list.status) {
                        case 'subscribed':
                            list.classNames = 'fa-check-circle text-success';
                            break;

                        case 'unsubscribed':
                            list.classNames = 'fa-times-circle text-danger';
                            break;

                        case 'unconfirmed':
                            list.classNames = 'fa-exclamation-circle text-warning';
                            break;

                        default:
                            list.classNames = '';
                            break;
                    }

                    return list;
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
            },

            unconfirmedLists() {
                return this.inLists.filter((list) => {
                    return list.status === 'unconfirmed';
                });
            }
        },

        watch: {
            editLists: 'updateListSubscription'
        },

        methods: {
            editInfo() {
                const lists = $.extend(true, [], this.subscriber.lists);

                this.informations = {
                    lists
                };

                this.editLists = this.subscriber.lists.filter((list) => {
                    return list.status === 'subscribed';
                }).map((list) => {
                    return list.id;
                });

                this.isEditingInformations = true;
            },

            canceEditInfo() {
                this.resetInformations();
            },

            saveEditInfo() {
                this.subscriber.lists = $.extend(true, [], this.informations.lists);

                this.resetInformations();
            },

            resetInformations() {
                this.isEditingInformations = false;
                this.editLists = [];
                this.informations = {
                    lists: [],
                    meta: {}
                };
            },

            updateListSubscription(newLists, oldLists) {
                // When we start editing informations, we first deep clone the original
                // subscriber.lists to a temporary informations.lists. We'll manipulate
                // this informations.lists and set it as subscriber.lists when editing is
                // saved. So, when we toggle any list checkbox, we can fetch the original
                // data from subscriber.lists and set it in informations.lists.
                let infoListNonSubList = {};
                let infoListSubList = {};
                let unconfirmedList = [];

                // First, with array diff, find out which id is to be subscribed or to be unsubscribed.
                // unconfirmed list will restore as status: unconfirmed, not unsubscribed
                const newSubId = _.chain(newLists).difference(oldLists).first().value();
                const newUnsubId = _.chain(oldLists).difference(newLists).first().value();

                if (newSubId) {
                    // We are subscribing to a new or an existing list here. First, check if this id is
                    // already in the temporary information.list or not.
                    infoListNonSubList = _.chain(this.informations.lists).filter((list) => {
                        return (list.id === newSubId) && (list.status !== 'subscribed');
                    }).first().value();

                    if (infoListNonSubList) {
                        // We have an existing list whose status is either unsubscribed or unconfirmed.
                        // Let's set the status as subscribed
                        infoListNonSubList.status = 'subscribed';

                    } else {
                        // Here we don't have any existing list in subscriber.lists, so we'll push a
                        // new list item in informations.list whose status is subscribed
                        const newSubList = _.find(this.lists, {
                            id: newSubId
                        });

                        if (newSubList && !_.find(this.informations.lists, { id: newSubId })) { // eslint-disable-line object-curly-newline
                            this.informations.lists.push({
                                id: newSubList.id,
                                status: 'subscribed',
                                subscribed_at: null,
                                unsubscribed_at: null
                            });
                        }
                    }
                }

                if (newUnsubId) {
                    // We are unsubscribing to an existing list here. First, make sure if this id is
                    // in the temporary information.list.
                    infoListSubList = _.chain(this.informations.lists).filter((list) => {
                        return (list.id === newUnsubId) && (list.status === 'subscribed');
                    }).first().value();

                    if (infoListSubList) {
                        // Before unsubscribing, let's check what's if it exists in subscriber.lists
                        unconfirmedList = this.subscriber.lists.filter((list) => {
                            return (list.id === infoListSubList.id);
                        });

                        if (unconfirmedList.length) {
                            // If original status is subscribed then unsubscribed it, otherwise set back
                            // the original status. Keep in mind that, user can check and then uncheck before
                            // save the changes.
                            infoListSubList.status = unconfirmedList[0].status === 'subscribed' ? 'unsubscribed' : unconfirmedList[0].status;

                        } else {
                            // The list doesn't exist in subscriber.lists
                            _.remove(this.informations.lists, (list) => {
                                return list.id === infoListSubList.id;
                            });
                        }
                    }
                }
            },

            showListInfo(listId) {
                let elem = '';
                let time = '';

                const list = _.find(this.subscriber.lists, {
                    id: listId
                });

                if (list) {
                    switch (list.status) {
                        case 'unconfirmed':
                            elem += '<span class="text-warning">';
                            elem += `status: ${this.i18n.unconfirmed}`;
                            elem += '</span>';
                            break;

                        case 'unsubscribed':
                            elem += '<span class="text-danger">';

                            time = moment
                                .tz(list.unsubscribed_at, 'YYYY-MM-DD HH:mm:ss', 'Etc/UTC')
                                .tz(weMail.dateTime.server.timezone)
                                .format(`${weMail.momentDateFormat} ${weMail.momentTimeFormat}`);

                            elem += `status: ${this.i18n.unsubscribed} @ ${time}`;
                            elem += '</span>';
                            break;

                        case 'subscribed':
                        default:
                            elem += '<span class="text-success">';

                            time = moment
                                .tz(list.subscribed_at, 'YYYY-MM-DD HH:mm:ss', 'Etc/UTC')
                                .tz(weMail.dateTime.server.timezone)
                                .format(`${weMail.momentDateFormat} ${weMail.momentTimeFormat}`);

                            elem += `status: ${this.i18n.subscribed} @ ${time}`;
                            elem += '</span>';
                            break;
                    }
                }

                return elem;
            },

            deleteItem() {
                const vm = this;

                this.deleteSubscriber(this.subscriber.id, () => {
                    vm.$router.push({
                        name: 'subscriberIndex'
                    });
                });
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
            position: relative;

            .subscriber-name {
                padding: 0 30px 0 0;
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

            .subscriber-action {
                position: absolute;
                top: 6px;
                right: 0;

                button {
                    height: auto;
                    padding: 3px 6px;
                    font-size: 16px;
                    line-height: 1;
                    color: #666;
                }
            }
        }

        .badge-tag {
            max-width: 140px;

            @include text-truncate;
        }

        .list-status-info {
            margin: 3px 0 10px !important;
            font-size: 11px;
            font-style: italic;
        }
    }
</style>
