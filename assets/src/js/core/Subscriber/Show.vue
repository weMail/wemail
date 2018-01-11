<template>
    <div v-if="isLoaded" class="wemail-single-subscriber">
        <div v-if="subscriber" class="row">
            <div class="col-md-5">
                <div class="wemail-box-plain clearfix">
                    <div :class="['profile-image', !hasUploadedImage ? 'gravatar-image' : '']">
                        <img :src="profileImage" alt="">

                        <button
                            type="button"
                            class="button button-link edit-image"
                            @click="setProfileImage"
                        >{{ __('Edit') }}</button>

                        <button
                            v-if="hasUploadedImage"
                            type="button"
                            class="button button-link remove-image"
                            @click="removeImage"
                        >{{ __('Remove') }}</button>
                    </div>

                    <div class="profile-summery">
                        <inline-editor v-model="name" :options="nameOptions" @input="updateNames">
                            <h1 class="subscriber-name">{{ fullName }}</h1>
                        </inline-editor>

                        <inline-editor v-model="subscriber.email" @input="updateSubscriber('email')">
                            <span>{{ subscriber.email }}</span>
                        </inline-editor>

                        <ul v-if="networks.length" class="subscriber-networks list-inline">
                            <li v-for="network in networks" class="list-inline-item">
                                <a :href="network.link" v-html="network.icon" target="_blank"></a>
                            </li>
                        </ul>

                        <div class="subscriber-action wemail-dropdown float-right">
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
                                    @click.prevent="deleteThisSubscriber"
                                >{{ __('Delete Subscriber') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wemail-box-plain">
                    <h4 class="box-title">
                        {{ __('Informations') }}
                    </h4>

                    <ul class="list-two-columns">
                        <li>
                            <label>{{ __('Lists') }}</label>
                            <div>
                                <p v-if="!inLists.length && !editLists">
                                    <a href="#" @click.prevent="editLists = true">{{ __('Add to lists') }}</a>
                                </p>

                                <template v-else-if="!editLists">
                                    <div class="editable-content" @click="editLists = true">
                                        <span v-for="list in inLists" class="badge-tag">
                                            <i :class="['fa', list.classNames]"></i> {{ list.name }}
                                        </span>
                                    </div>
                                </template>

                                <template v-else>
                                    <ul>
                                        <li v-for="list in lists">
                                            <label>
                                                <input type="checkbox" :value="list.id" v-model="subscribedLists"> {{ list.name }}
                                                <p class="list-status-info" v-html="showListInfo(list.id)"></p>
                                            </label>
                                        </li>
                                    </ul>

                                    <div class="clearfix">
                                        <button
                                            type="button"
                                            class="button button-link"
                                            @click="editLists = false"
                                        >{{ __('Cancel') }}</button>

                                        <button
                                            type="button"
                                            class="button button-small button-primary float-right"
                                            @click="updateLists"
                                        >{{ __('Update Lists') }}</button>
                                    </div>
                                </template>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-7">
                <pre>{{ subscribedLists }}</pre>
                <pre>{{ subscriber.lists }}</pre>
            </div>
        </div>

        <div v-else class="error"><p>{{ __('Subscriber not found') }}</p></div>
    </div>
</template>

<script>
    import deleteSubscriber from './mixins/deleteSubscriber.js';

    export default {
        routeName: 'subscriberShow',

        mutations: {
            updateSubscriber(state, payload) {
                state.subscriber = payload;
            }
        },

        mixins: [
            ...weMail.getMixins('routeComponent', 'helpers', 'imageUploader'),
            deleteSubscriber
        ],

        data() {
            return {
                fileFrame: null,
                editLists: false,
                subscribedLists: []
            };
        },

        computed: {
            ...Vuex.mapState('subscriberShow', ['i18n', 'subscriber', 'lists', 'socialNetworks']),

            meta() {
                return this.subscriber.meta;
            },

            fullName() {
                if (!this.subscriber.first_name) {
                    return __('no name');
                }

                const name = [this.subscriber.first_name, this.subscriber.last_name];

                return name.join(' ');
            },

            name: {
                get() {
                    return {
                        firstName: this.subscriber.first_name || '',
                        lastName: this.subscriber.last_name || ''
                    };
                },

                set(name) {
                    this.subscriber.first_name = name.firstName;
                    this.subscriber.last_name = name.lastName;
                }
            },

            nameOptions() {
                return [
                    {
                        label: __('First Name'),
                        name: 'firstName'
                    },
                    {
                        label: __('Last Name'),
                        name: 'lastName'
                    }
                ];
            },

            hasUploadedImage() {
                return (this.subscriber.image && this.subscriber.image.sizes && this.subscriber.image.sizes.thumbnail);
            },

            profileImage() {
                let src = '';

                if (this.hasUploadedImage) {
                    src = weMail.siteURL + this.subscriber.image.sizes.thumbnail.url;
                } else {
                    src = `https://www.gravatar.com/avatar/${this.md5(this.subscriber.email)}?d=mm&s=100`;
                }

                return src;
            },

            networks() {
                const vm = this;
                const networks = [];

                if (!vm.subscriber.meta) {
                    vm.subscriber.meta = {};
                }

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

                if (!this.subscriber.lists) {
                    this.subscriber.lists = [];
                }

                const lists = this.subscriber.lists.map((subscriberist) => {
                    const list = $.extend(true, {}, subscriberist);

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
            }
        },

        watch: {
            editLists: 'onEditLists'
        },

        methods: {
            updateSubscriber(prop) {
                const vm = this;
                const data = {};

                data[prop] = this.subscriber[prop];

                weMail.api.subscribers(this.subscriber.id).update(data).done((response) => {
                    vm.$store.commit('subscriberShow/updateSubscriber', response.data);
                });
            },

            deleteThisSubscriber() {
                const vm = this;

                this.deleteSubscriber(this.subscriber.id, () => {
                    vm.$router.push({
                        name: 'subscriberIndex'
                    });
                }, true);
            },

            setProfileImage() {
                this.openImageManager();
            },

            onSelectImages(images) {
                const image = images[0];

                const sizes = {};

                _.forEach(image.sizes, (img, size) => {
                    sizes[size] = {
                        width: img.width,
                        height: img.height,
                        url: img.url.replace(weMail.siteURL, '')
                    };
                });

                this.subscriber.image = {
                    id: image.id,
                    name: image.name,
                    alt: image.alt,
                    sizes
                };

                this.updateSubscriber('image');
            },

            removeImage() {
                this.subscriber.image = null;
                this.updateSubscriber('image');
            },

            updateNames() {
                this.updateSubscriber('first_name');
                this.updateSubscriber('last_name');
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
                            elem += __('Unconfirmed');
                            elem += '</span>';
                            break;

                        case 'unsubscribed':
                            elem += '<span class="text-danger">';

                            time = moment
                                .tz(list.unsubscribed_at, 'YYYY-MM-DD HH:mm:ss', 'Etc/UTC')
                                .tz(weMail.dateTime.server.timezone)
                                .format(`${weMail.momentDateFormat} ${weMail.momentTimeFormat}`);

                            elem += `${__('Unsubscribed')} @ ${time}`;
                            elem += '</span>';
                            break;

                        case 'subscribed':
                        default:
                            elem += '<span class="text-success">';

                            time = moment
                                .tz(list.subscribed_at, 'YYYY-MM-DD HH:mm:ss', 'Etc/UTC')
                                .tz(weMail.dateTime.server.timezone)
                                .format(`${weMail.momentDateFormat} ${weMail.momentTimeFormat}`);

                            elem += `${__('Subscribed')} @ ${time}`;
                            elem += '</span>';
                            break;
                    }
                }

                return elem;
            },

            onEditLists(edit) {
                if (edit) {
                    this.subscribedLists = this.inLists.filter((lists) => {
                        return lists.status === 'subscribed';
                    }).map((list) => {
                        return list.id;
                    });
                }
            },

            updateLists() {
                const vm = this;

                const data = {
                    lists: this.subscribedLists.length ? this.subscribedLists : null
                };

                weMail.api.subscribers(this.subscriber.id).update(data).done((response) => {
                    vm.$store.commit('subscriberShow/updateSubscriber', response.data);
                    vm.editLists = false;
                });
            }
        }
    };
</script>

<style lang="scss">
    .wemail-single-subscriber {
        padding-top: 10px;

        .profile-image {
            position: relative;
            display: inline-block;
            float: left;
            width: 100px;
            height: 100px;
            padding: 4px;
            margin-right: 10px;
            line-height: 0;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);

            img {
                max-width: 100%;
                border: 1px solid $wp-border-color-darken;
            }

            button.button-link {
                position: absolute;
                left: 0;
                z-index: 1;
                width: 100%;
                height: 50%;
                color: #fff;
                text-align: center;
                background-color: rgba(0, 0, 0, 0.75);
                opacity: 0;

                @include transition;

                &:hover {
                    color: $wp-blue;
                    background-color: rgba(0, 0, 0, 0.75);
                }

                &.edit-image {
                    top: 0;
                }

                &.remove-image {
                    bottom: 0;
                    border-top: 1px solid #afafaf;
                }

                &:focus {
                    box-shadow: none;
                }
            }

            &:hover {

                button {
                    opacity: 1;
                }
            }

            &.gravatar-image {

                button.button-link.edit-image {
                    height: 100%;
                }
            }
        }

        .profile-summery {
            position: relative;
            float: left;
            width: calc(100% - 110px);

            .subscriber-name {
                padding: 0;
                margin: 0 30px 5px 0;
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
