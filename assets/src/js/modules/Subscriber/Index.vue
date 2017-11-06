<template>
    <div v-if="isLoaded">
        <h1>
            {{ i18n.subscribers }}
            <a
                href="#add-new-subscriber"
                class="page-title-action"
                @click.prevent="showNewSubscriberModal"
            >{{ i18n.addNew }}</a>

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

        <new-subscriber-modal
            scope="subscriber-index"
            :i18n="i18n"
            :lists="lists"
            @subscriber-created="fetchData"
        ></new-subscriber-modal>
    </div>
</template>

<script>
    import NewSubscriberModal from './components/NewSubscriberModal.vue';
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
            NewSubscriberModal,
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
                        onClick: 'onClickRowActionQuickEdit'
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

            showNewSubscriberModal() {
                weMail.event.$emit('show-new-subscriber-modal-subscriber-index');
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

                return weMail.userCaps.edit_subscriber;
            },

            onClickRowActionQuickEdit(subscriber) {
                console.log('onClickEditAction');
                console.log(subscriber);
            },

            showRowActionTrash(subscriber, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.userCaps.delete_subscriber;
            },

            onClickRowActionTrash(subscriber) {
                this.onClickRowActionDelete(subscriber, true);
            },

            showRowActionDelete(subscriber, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.userCaps.delete_subscriber;
            },

            onClickRowActionDelete(subscriber, softDelete) {
                const vm = this;

                this.deleteSubscriber(subscriber.id, () => {
                    vm.fetchData();
                }, softDelete);
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

                return weMail.userCaps.delete_subscriber;
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
                return !this.showBulkActionTrash(currentRoute);
            },

            showBulkActionRestore(currentRoute) {
                return (currentRoute.params.status === 'trashed') || false;
            },

            onBulkAction(action, ids) {
                if (action === 'delete') {
                    this.deleteBulkSubscribers(ids);
                } else if (action === 'trash') {
                    this.deleteBulkSubscribers(ids, true);
                } else if (action === 'restore') {
                    this.restoreBulkSubscribers(ids);
                }
            },

            deleteBulkSubscribers(ids, softDelete) {
                const vm = this;

                vm.deleteSubscriber(ids, () => {
                    vm.fetchData();
                }, softDelete);
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

                return {
                    text: `<span class="text-truncate">${name}</span>`,
                    classNames: ['list-table-title'],
                    route: {
                        name: 'subscriberShow',
                        params: {
                            id: subscriber.id
                        }
                    }
                };
            },

            columnEmailAddress(subscriber) {
                return `<span class="text-truncate">${subscriber.email}</span>`;
            },

            columnPhone(subscriber) {
                return subscriber.phone ? `<span class="text-truncate">${subscriber.phone}</span>` : '-';
            },

            columnLifeStage(subscriber) {
                return this.i18n[subscriber.life_stage];
            },

            columnCreatedAt(subscriber) {
                return this.toWPDate(subscriber.created_at);
            }
        }
    };
</script>
