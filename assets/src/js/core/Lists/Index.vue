<template>
    <div v-if="isLoaded">
        <h1>
            {{ __('Lists') }}

            <router-link
                :to="{name: 'listsCreate'}"
                class="page-title-action"
            >{{ __('Add New') }}</router-link>
        </h1>

        <list-table
            name="lists"
            :i18n="i18n"
            :filter-menu="filterMenu"
            :bulk-actions="bulkActions"
            :table-data="lists"
            :columns="listTable.columns"
            :sortable-columns="listTable.sortableColumns"
            :row-actions="rowActions"
            @bulk-action="onBulkAction"
        >
            <input slot="search" type="search" v-model="search" :placeholder="__('Search Lists')">

            <span slot="no-data-found">{{ __('No List Found') }}</span>
        </list-table>

        <router-view></router-view>
    </div>
</template>

<script>
    import deleteList from './mixins/deleteList.js';

    const ListTable = weMail.components.ListTable;

    export default {
        routeName: 'listsIndex',

        mutations: {
            updateLists(state, payload) {
                state.lists = payload;
            }
        },

        mixins: [
            ...weMail.getMixins('routeComponent', 'helpers'),
            deleteList
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
                },

                filterMenus: {
                    all: __('All'),
                    public: __('Public'),
                    private: __('Private')
                },

                i18n: {
                    name: __('Name'),
                    description: __('Description'),
                    subscribed: __('Subscribed'),
                    unsubscribed: __('Unsubscribed'),
                    unconfirmed: __('Unconfirmed'),
                    createdAt: __('Created At')
                }
            };
        },

        computed: {
            ...Vuex.mapState('listsIndex', ['lists', 'listTable']),

            filterMenu() {
                return _.map(this.lists.meta.filter_menu, (count, name) => {
                    const menu = {
                        name,
                        count,
                        title: this.filterMenus[name],
                        route: {}
                    };

                    switch (name) {
                        case 'all':
                            menu.route = {
                                name: 'listsIndex'
                            };
                            break;

                        default:
                            menu.route = {
                                name: 'listsIndexType',
                                params: {
                                    type: name
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
                        name: 'delete',
                        title: __('Delete')
                    }
                ];
            },

            rowActions() {
                return [
                    {
                        action: 'edit',
                        title: __('Edit'),
                        classNames: ['edit'],
                        route: 'rowActionEditRoute'
                    },
                    {
                        action: 'viewSubscribers',
                        title: __('View Subscribers'),
                        classNames: ['view'],
                        route: 'rowActionViewSubscribersRoute'
                    },
                    {
                        action: 'delete',
                        title: __('Delete'),
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

                if (vm.$router.currentRoute.params.type) {
                    query.type = vm.$router.currentRoute.params.type;
                }

                vm.apiHandler = weMail
                    .api
                    .lists()
                    .query(query)
                    .get()
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('listsIndex/updateLists', response);
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

            rowActionEditRoute(list) {
                return {
                    name: 'listsEdit',
                    params: {
                        id: list.id
                    }
                };
            },

            rowActionViewSubscribersRoute(list) {
                return {
                    name: 'listsSubscribers',
                    params: {
                        id: list.id
                    }
                };
            },

            showRowActionDelete(list) {
                return !(list.type === 'protected');
            },

            onClickRowActionDelete(list) {
                const vm = this;

                vm.deleteList(list.id, () => {
                    vm.fetchData();
                });
            },

            onBulkAction(action, ids) {
                if (action === 'delete') {
                    this.deleteBulkLists(ids);
                }
            },

            deleteBulkLists(ids) {
                const vm = this;

                vm.deleteList(ids, () => {
                    vm.fetchData();
                });
            },

            columnName(list) {
                let icon = '';

                if (list.type === 'private') {
                    icon += `<span title="${__('This list is private')}" class="dashicons dashicons-hidden"></span>`;

                }

                return {
                    html: `
                        <span>
                            <router-link :to="{name: 'listsSubscribers', params: {id: '${list.id}'}}" class="list-table-title">
                                ${list.name}
                            </router-link> ${icon}
                        </span>
                    `
                };
            },

            columnCreatedAt(list) {
                return this.toWPDateTime(list.created_at);
            }
        }
    };
</script>

<style lang="scss">
    .wemail-list-table-lists {

        .column-subscribed,
        .column-unsubscribed,
        .column-unconfirmed {
            width: 130px;
        }
    }
</style>
