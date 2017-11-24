<template>
    <div v-if="isLoaded">
        <h1>
            {{ i18n.forms }}

            <router-link
                :to="{name: 'formCreate'}"
                class="page-title-action"
            >{{ i18n.addNew }}</router-link>
        </h1>

        <list-table
            name="forms"
            :i18n="i18n"
            :filter-menu="filterMenu"
            :bulk-actions="bulkActions"
            :table-data="forms"
            :columns="listTable.columns"
            :sortable-columns="listTable.sortableColumns"
            :row-actions="rowActions"
            @bulk-action="onBulkAction"
        >
            <input slot="search" type="search" v-model="search" :placeholder="i18n.searchForms">

            <span slot="no-data-found">{{ i18n.noFormFound }}</span>
        </list-table>

        <router-view></router-view>
    </div>
</template>

<script>
    const ListTable = weMail.components.ListTable;

    export default {
        routeName: 'formIndex',

        mutations: {
            updateForms(state, payload) {
                state.forms = payload;
            }
        },

        mixins: [
            ...weMail.getMixins('routeComponent', 'helpers')
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
            ...Vuex.mapState('formIndex', ['i18n', 'forms', 'listTable']),

            filterMenu() {
                return _.map(this.forms.meta.filter_menu, (count, name) => {
                    const menu = {
                        name,
                        count,
                        title: this.i18n[name],
                        route: {}
                    };

                    switch (name) {
                        case 'all':
                            menu.route = {
                                name: 'formIndex'
                            };
                            break;

                        case 'trashed':
                        default:
                            menu.route = {
                                name: 'formIndexStatus',
                                params: {
                                    status: name
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

                vm.apiHandler = weMail
                    .api
                    .forms()
                    .query(query)
                    .get()
                    .done((response) => {
                        if (response.data) {
                            vm.$store.commit('formIndex/updateForms', response);
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

            deleteForm(id, callback, softDelete) {
                const vm = this;
                const warn = {
                    confirmButtonText: vm.i18n.delete,
                    cancelButtonText: vm.i18n.cancel
                };

                let api = weMail.api;

                if (_.isArray(id)) {
                    warn.text = vm.i18n.deleteFormsWarnMsg;
                    api = api.forms().query({
                        ids: id
                    });
                } else {
                    warn.text = vm.i18n.deleteFormWarnMsg;
                    api = api.forms(id);
                }

                if (softDelete) {
                    api = api.query({
                        soft_delete: true
                    });
                }

                if (!softDelete) {
                    vm.warn(warn).then((deleteIt) => {
                        if (deleteIt) {
                            api.delete().done((response) => {
                                // this success alert will be replaced by some notification library
                                vm.success({
                                    text: vm.i18n.formDeleted,
                                    confirmButtonText: vm.i18n.close
                                }).then(() => {
                                    if (typeof callback === 'function') {
                                        callback(response);
                                    }
                                });
                            });
                        } else {
                            api.reset();
                        }
                    });

                } else {
                    api.delete().done((response) => {
                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    });
                }
            },

            restoreForm(id, callback) {
                let api = weMail.api;

                if (_.isArray(id)) {
                    api = api.forms().restore()
                        .query({
                            ids: id
                        });
                } else {
                    api = api.forms(id).restore();
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
                    this.deleteBulkForms(ids);
                } else if (action === 'trash') {
                    this.deleteBulkForms(ids, true);
                } else if (action === 'restore') {
                    this.restoreBulkForms(ids);
                }
            },

            deleteBulkForms(ids, softDelete) {
                const vm = this;

                vm.deleteForm(ids, () => {
                    vm.fetchData();
                }, softDelete);
            },

            restoreBulkForms(ids) {
                const vm = this;

                vm.restoreForm(ids, () => {
                    vm.fetchData();
                });
            },

            showRowActionEdit(form, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.user.permissions.edit_form;
            },

            rowActionEditRoute(form) {
                return {
                    name: 'formEdit',
                    params: {
                        id: form.id
                    }
                };
            },

            showRowActionTrash(form, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_form;
            },

            onClickRowActionTrash(form) {
                this.onClickRowActionDelete(form, true);
            },

            showRowActionView(form, currentRoute) {
                if (currentRoute.params.status === 'trashed') {
                    return false;
                }

                return true;
            },

            rowActionViewRoute(form) {
                return {
                    name: 'formEdit',
                    params: {
                        id: form.id
                    }
                };
            },

            showRowActionRestore(form, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_form;
            },

            onClickRowActionRestore(form) {
                const vm = this;

                this.restoreForm(form.id, () => {
                    vm.fetchData();
                });
            },

            showRowActionDelete(form, currentRoute) {
                if (currentRoute.params.status !== 'trashed') {
                    return false;
                }

                return weMail.user.permissions.delete_form;
            },

            onClickRowActionDelete(form, softDelete) {
                const vm = this;

                this.deleteForm(form.id, () => {
                    vm.fetchData();
                }, softDelete);
            },

            columnName(form) {
                return {
                    html: `
                        <router-link :to="{name: 'formEdit', params: {id: '${form.id}'}}" class="list-table-title">
                            ${form.name}
                        </router-link>
                    `
                };
            },

            columnCreatedAt(form) {
                return this.toWPDate(form.created_at);
            }
        }
    };
</script>

<style lang="scss">
    .wemail-list-table-forms {

        .column-entries {
            width: 130px;
        }
    }
</style>
