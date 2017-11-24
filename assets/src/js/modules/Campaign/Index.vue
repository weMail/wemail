<template>
    <div v-if="isLoaded">
        <h1>
            {{ i18n.campaigns }}
            <router-link
                :to="{name: 'campaignCreate'}"
                class="page-title-action"
            >{{ i18n.addNew }}</router-link>
        </h1>

        <list-table
            name="campaigns"
            :i18n="i18n"
            :filter-menu="filterMenu"
            :bulk-actions="bulkActions"
            :table-data="campaigns"
            :columns="listTable.columns"
            :sortable-columns="listTable.sortableColumns"
            :row-actions="rowActions"
            @bulk-action="onBulkAction"
        >
            <input slot="search" type="search" v-model="search" :placeholder="i18n.searchLists">

            <span slot="no-data-found">{{ i18n.noListFound }}</span>
        </list-table>
    </div>
</template>

<script>
    import deleteCampaign from './mixins/deleteCampaign.js';

    const ListTable = weMail.components.ListTable;

    export default {
        routeName: 'campaignIndex',

        mutations: {
            updateCampaigns(state, payload) {
                state.campaigns = payload;
            }
        },

        mixins: [
            ...weMail.getMixins('routeComponent', 'helpers'),
            deleteCampaign
        ],

        components: {
            ListTable
        },

        data() {
            return {
                testString: 'this is from index.vue',
                search: '',
                apiHandler: {
                    abort() {
                        //
                    }
                }
            };
        },

        computed: {
            ...Vuex.mapState('campaignIndex', ['i18n', 'campaigns', 'listTable']),

            filterMenu() {
                return _.map(this.campaigns.meta.filter_menu, (count, name) => {
                    const menu = {
                        name,
                        count,
                        title: this.i18n[name],
                        route: {}
                    };

                    switch (name) {
                        case 'all':
                            menu.route = {
                                name: 'campaignIndex'
                            };
                            break;

                        case 'automatic':
                        case 'standard':
                            menu.route = {
                                name: 'campaignIndexType',
                                params: {
                                    type: name
                                }
                            };
                            break;

                        case 'active':
                        case 'paused':
                        case 'draft':
                        case 'sent':
                            menu.route = {
                                name: 'campaignIndexStatus',
                                params: {
                                    status: name
                                }
                            };
                            break;

                        case 'trashed':
                        default:
                            menu.route = {
                                name: 'campaignIndexStatus',
                                params: {
                                    status: 'trashed'
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
                        action: 'edit',
                        title: this.i18n.edit,
                        classNames: ['edit'],
                        showIf: 'showRowActionEdit',
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

                if (vm.$router.currentRoute.params.status) {
                    query.status = vm.$router.currentRoute.params.status;
                }

                if (vm.$router.currentRoute.params.type) {
                    query.type = vm.$router.currentRoute.params.type;
                }

                vm.apiHandler = weMail
                    .api
                    .campaigns()
                    .query(query)
                    .get()
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('campaignIndex/updateCampaigns', response);
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

            restoreCampaign(id, callback) {
                let api = weMail.api;

                if (_.isArray(id)) {
                    api = api.campaigns().restore()
                        .query({
                            ids: id
                        });
                } else {
                    api = api.campaigns(id).restore();
                }

                api.put().done((response) => {
                    if (typeof callback === 'function') {
                        callback(response);
                    }
                });
            },

            showBulkActionTrash(currentRoute) {
                return !(currentRoute.params.status === 'trashed') || false;
            },

            showBulkActionRestore(currentRoute) {
                return (currentRoute.params.status === 'trashed') || false;
            },

            showBulkActionDelete(currentRoute) {
                return !this.showBulkActionTrash(currentRoute);
            },

            onBulkAction(action, ids) {
                if (action === 'delete') {
                    this.deleteBulkCampaigns(ids);
                } else if (action === 'trash') {
                    this.deleteBulkCampaigns(ids, true);
                } else if (action === 'restore') {
                    this.restoreBulkCampaigns(ids);
                }
            },

            deleteBulkCampaigns(ids, softDelete) {
                const vm = this;

                vm.deleteCampaign(ids, () => {
                    vm.fetchData();
                }, softDelete);
            },

            restoreBulkCampaigns(ids) {
                const vm = this;

                vm.restoreCampaign(ids, () => {
                    vm.fetchData();
                });
            },

            showRowActionEdit(campaign, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.user.permissions.edit_campaign;
            },

            rowActionEditRoute(campaign) {
                return {
                    name: 'campaignEdit',
                    params: {
                        id: campaign.id
                    }
                };
            },

            showRowActionTrash(campaign, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_campaign;
            },

            onClickRowActionTrash(campaign) {
                this.onClickRowActionDelete(campaign, true);
            },

            showRowActionView(campaign, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return true;
            },

            rowActionViewRoute(campaign) {
                return {
                    name: 'campaignShow',
                    params: {
                        id: campaign.id
                    }
                };
            },

            showRowActionRestore(campaign, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_campaign;
            },

            onClickRowActionRestore(campaign) {
                const vm = this;

                this.restoreCampaign(campaign.id, () => {
                    vm.fetchData();
                });
            },

            showRowActionDelete(campaign, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_campaign;
            },

            onClickRowActionDelete(campaign, softDelete) {
                const vm = this;

                this.deleteCampaign(campaign.id, () => {
                    vm.fetchData();
                }, softDelete);
            },

            columnName(campaign) {
                return {
                    html: `
                        <router-link :to="{name: 'campaignShow', params: {id: '${campaign.id}'}}" class="list-table-title">
                            ${campaign.name}
                        </router-link>
                    `
                };
            },

            columnLists(campaign) {
                const lists = [];

                campaign.lists.forEach((listId) => {
                    const list = _.find(weMail.lists, {
                        id: listId
                    });

                    if (list) {
                        lists.push(list.name);
                    }
                });

                return lists.join(', ');
            }
        }
    };
</script>

<style lang="scss">
    .wemail-list-table-campaigns {

        .column-status {
            width: 180px;
        }

        .column-lists {
            width: 155px;
        }

        .column-opened,
        .column-clicked,
        .column-bounced,
        .column-unsubscribed {
            width: 85px;
        }
    }
</style>
