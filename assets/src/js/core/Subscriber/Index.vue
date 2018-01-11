<template>
    <div v-if="isLoaded">
        <h1>
            {{ __('Subscribers') }}

            <router-link
                :to="{name: 'subscriberCreate'}"
                class="page-title-action"
            >{{ __('Add New') }}</router-link>

            <a
                href="#"
                class="page-title-action"
            >{{ i18n.searchSegment }}</a>
        </h1>

        <list-table
            name="subscribers"
            :i18n="i18n"
            :filter-menu="filterMenu"
            :bulk-actions="bulkActions"
            :table-data="subscribers"
            :columns="listTable.columns"
            :sortable-columns="listTable.sortableColumns"
            :row-actions="rowActions"
            @bulk-action="onBulkAction"
        >
            <input slot="search" type="search" v-model="search" :placeholder="i18n.searchSubscribers">

            <span slot="no-data-found">{{ i18n.noSubscriberFound }}</span>
        </list-table>

        <router-view></router-view>
    </div>
</template>

<script>
    import deleteSubscriber from './mixins/deleteSubscriber.js';
    import restoreSubscriber from './mixins/restoreSubscriber.js';

    const ListTable = weMail.components.ListTable;

    export default {
        routeName: 'subscriberIndex',

        mutations: {
            updateSubscribers(state, payload) {
                state.subscribers = payload;
            }
        },

        mixins: [
            ...weMail.getMixins('routeComponent', 'helpers'),
            deleteSubscriber,
            restoreSubscriber
        ],

        components: {
            ListTable
        },

        data() {
            return {
                search: '',
                apiHandler: {
                    abort() {
                        //
                    }
                }
            };
        },

        computed: {
            ...Vuex.mapState('subscriberIndex', ['i18n', 'subscribers', 'lists', 'listTable']),

            filterMenu() {
                return _.map(this.subscribers.meta.filter_menu, (count, name) => {
                    const menu = {
                        name,
                        count,
                        title: this.i18n[name],
                        route: {}
                    };

                    switch (name) {
                        case 'all':
                            menu.route = {
                                name: 'subscriberIndex'
                            };
                            break;

                        case 'trashed':
                            menu.route = {
                                name: 'subscriberIndexStatus',
                                params: {
                                    status: 'trashed'
                                }
                            };
                            break;

                        default:
                            menu.route = {
                                name: 'subscriberIndexLifeStage',
                                params: {
                                    lifeStage: name
                                }
                            };
                            break;
                    }

                    return menu;
                });
            },

            bulkActions() {
                return [
                    {
                        name: 'trash',
                        title: this.i18n.moveToTrash,
                        showIf: 'showBulkActionTrash'
                    },
                    {
                        name: 'restore',
                        title: this.i18n.restore,
                        showIf: 'showBulkActionRestore'
                    },
                    {
                        name: 'delete',
                        title: this.i18n.deletePermanently,
                        showIf: 'showBulkActionDelete'
                    }
                ];
            },

            rowActions() {
                return [
                    {
                        action: 'quickEdit',
                        title: this.i18n.quickEdit,
                        classNames: ['quick-edit'],
                        showRowIf: 'showRowActionQuickEdit',
                        route: 'rowActionEditRoute'
                    },
                    {
                        action: 'trash',
                        title: this.i18n.trash,
                        classNames: ['trash'],
                        showIf: 'showRowActionTrash',
                        onClick: 'onClickRowActionTrash'
                    },
                    {
                        action: 'view',
                        title: this.i18n.view,
                        classNames: ['view'],
                        showIf: 'showRowActionView',
                        route: 'rowActionViewRoute'
                    },
                    {
                        action: 'restore',
                        title: this.i18n.restore,
                        classNames: ['restore'],
                        showIf: 'showRowActionRestore',
                        onClick: 'onClickRowActionRestore'
                    },
                    {
                        action: 'delete',
                        title: this.i18n.deletePermanently,
                        classNames: ['delete'],
                        showIf: 'showRowActionDelete',
                        onClick: 'onClickRowActionDelete'
                    }
                ];
            }
        },

        beforeMount() {
            this.search = this.$router.currentRoute.query.s;
        },

        watch: {
            '$route.query': 'fetchData',
            search: 'onChangeSearch'
        },

        methods: {
            fetchData() {
                const vm = this;

                vm.apiHandler.abort();

                const query = vm.$router.currentRoute.query;

                if (vm.$router.currentRoute.params.lifeStage) {
                    query.life_stage = vm.$router.currentRoute.params.lifeStage;
                }

                if (vm.$router.currentRoute.params.status) {
                    query.status = vm.$router.currentRoute.params.status;
                }

                vm.apiHandler = weMail
                    .api
                    .subscribers()
                    .query(query)
                    .get()
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('subscriberIndex/updateSubscribers', response);
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

            showRowActionQuickEdit(subscriber, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.user.permissions.edit_subscriber;
            },

            rowActionEditRoute(subscriber) {
                return {
                    name: 'subscriberEdit',
                    params: {
                        id: subscriber.id
                    }
                };
            },

            showRowActionTrash(subscriber, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_subscriber;
            },

            onClickRowActionTrash(subscriber) {
                const vm = this;

                this.deleteSubscriber(subscriber.id, () => {
                    vm.fetchData();
                });
            },

            showRowActionDelete(subscriber, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_subscriber;
            },

            onClickRowActionDelete(subscriber) {
                const vm = this;

                this.deleteSubscriber(subscriber.id, () => {
                    vm.fetchData();
                }, true);
            },

            showRowActionView(subscriber, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return true;
            },

            rowActionViewRoute(subscriber) {
                return {
                    name: 'subscriberShow',
                    params: {
                        id: subscriber.id
                    }
                };
            },

            showRowActionRestore(subscriber, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_subscriber;
            },

            onClickRowActionRestore(subscriber) {
                const vm = this;

                this.restoreSubscriber(subscriber.id, () => {
                    vm.fetchData();
                });
            },

            showBulkActionTrash(currentRoute) {
                return !(currentRoute.params.status === 'trashed') || false;
            },

            showBulkActionDelete(currentRoute) {
                return (currentRoute.params.status === 'trashed');
            },

            showBulkActionRestore(currentRoute) {
                return (currentRoute.params.status === 'trashed') || false;
            },

            onBulkAction(action, ids) {
                if (action === 'delete') {
                    this.deleteBulkSubscribers(ids, true);
                } else if (action === 'trash') {
                    this.deleteBulkSubscribers(ids);
                } else if (action === 'restore') {
                    this.restoreBulkSubscribers(ids);
                }
            },

            deleteBulkSubscribers(ids, permaDelete) {
                const vm = this;

                vm.deleteSubscriber(ids, () => {
                    vm.fetchData();
                }, permaDelete);
            },

            restoreBulkSubscribers(ids) {
                const vm = this;

                vm.restoreSubscriber(ids, () => {
                    vm.fetchData();
                });
            },

            columnName(subscriber) {
                let name = [subscriber.first_name, subscriber.last_name].join(' ').trim();

                if (!name) {
                    name = `(${this.i18n.noName})`;
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
                            <router-link :to="{name: 'subscriberShow', params: {id: '${subscriber.id}'}}" class="list-table-title">
                                ${name}
                            </router-link>
                        </div>
                    `
                };
            },

            columnEmailAddress(subscriber) {
                return `
                    <span class="text-truncate">${subscriber.email}</span>
                `;
            },

            columnPhone(subscriber) {
                return subscriber.phone ? `<span class="text-truncate">${subscriber.phone}</span>` : '-';
            },

            columnLifeStage(subscriber) {
                return this.i18n[subscriber.life_stage];
            },

            columnCreatedAt(subscriber) {
                return this.toWPDateTime(subscriber.created_at);
            }
        }
    };
</script>

<style lang="scss">
    .wemail-list-table-subscribers {

        .column-name {

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
</style>
