<template>
    <div v-if="isLoaded">
        <h1>
            {{ i18n.lists }}

            <router-link
                :to="{name: 'listsCreate'}"
                class="page-title-action"
            >{{ i18n.addNew }}</router-link>
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
            <input slot="search" type="search" v-model="search" :placeholder="i18n.searchLists">

            <span slot="no-data-found">{{ i18n.noListFound }}</span>
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
                }
            };
        },

        computed: {
            ...Vuex.mapState('listsIndex', ['i18n', 'lists', 'listTable']),

            filterMenu() {
                return _.map(this.lists.meta.filter_menu, (count, name) => {
                    const menu = {
                        name,
                        count,
                        title: this.i18n[name],
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
                        title: this.i18n.delete
                    }
                ];
            },

            rowActions() {
                return [
                    {
                        action: 'edit',
                        title: this.i18n.edit,
                        classNames: ['edit'],
                        route: 'rowActionEditRoute'
                    },
                    {
                        action: 'viewSubscribers',
                        title: this.i18n.viewSubscribers,
                        classNames: ['view'],
                        route: 'rowActionViewSubscribersRoute'
                    },
                    {
                        action: 'delete',
                        title: this.i18n.delete,
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
                return {
                    text: list.name,
                    classNames: ['list-table-title'],
                    route: {
                        name: 'listsSubscribers',
                        params: {
                            id: list.id
                        }
                    }
                };
            },

            columnCreatedAt(list) {
                return this.toWPDate(list.created_at);
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
