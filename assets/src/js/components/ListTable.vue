<template>
    <div :class="['wemail-list-table', wrapperClassName]">
        <form>
            <fieldset>
                <div class="clearfix">
                    <ul v-if="filterMenu.length" class="list-sub has-count filter-menu">
                        <li v-for="menu in filterMenu" :class="[filterMenuClass(menu.route)]">
                            <router-link :to="menu.route">
                                {{ menu.title }} <span class="count">({{ menu.count }})</span>
                            </router-link>
                        </li>
                    </ul>

                    <p v-if="$slots.search" class="list-table-search">
                        <slot name="search"></slot>
                    </p>
                </div>

                <div class="clearfix">
                    <div class="list-table-filter-dropdowns">
                        <div v-if="bulkActions.length" class="list-table-bulk-action">
                            <select v-model="bulkActionSelected">
                                <option value="">{{ i18n.bulkActions }}</option>
                                <option v-for="action in bulkActionOptions" :value="action.name">{{ action.title }}</option>
                            </select>

                            <button type="button" class="button" @click="triggerBulkAction">{{ i18n.apply }}</button>
                        </div>

                        <slot name="filter-dropdown"></slot>
                    </div>

                    <div class="float-right">
                        <div class="list-table-pagination">
                            <ul class="list-inline">
                                <li v-if="pagination.total" class="list-inline-item total-items">
                                    {{ pagination.total }} {{ i18n.items }}
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    :class="['list-inline-item', pagination.currentPage === 1 ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.firstPageRoute">«</router-link>
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    class="list-inline-item"
                                    :class="['list-inline-item', pagination.currentPage === 1 ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.prevPageRoute">‹</router-link>
                                </li>
                                <li v-if="pagination.hasPagination" class="list-inline-item">
                                    <input
                                        type="text"
                                        :style="{width: currentPageNumberInputWidth}"
                                        v-model="currentPageNumber"
                                    >
                                </li>
                                <li v-if="pagination.hasPagination" class="list-inline-item">
                                    of {{ pagination.lastPage }}
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    :class="['list-inline-item', pagination.currentPage === pagination.lastPage ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.nextPageRoute">›</router-link>
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    :class="['list-inline-item', pagination.currentPage === pagination.lastPage ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.lastPageRoute">»</router-link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="column-checkbox">
                                <input type="checkbox" :checked="isToggleSelectAllChecked" @change="toggleSelectAllItems">
                            </th>

                            <template v-for="column in columns">
                                <th
                                    v-if="!sortableColumns[column]"
                                    :class="columnTitles[column].classNames"
                                    v-html="columnTitles[column].title"
                                ></th>

                                <th
                                    v-else
                                    :class="columnTitles[column].classNames"
                                >
                                    <router-link :to="columnTitles[column].route">
                                        <span>{{ columnTitles[column].title }}</span>
                                        <span class="sorting-indicator"></span>
                                    </router-link>
                                </th>
                            </template>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-if="records.length">
                            <tr v-for="(record, recordIndex) in records">
                                <td class="column-checkbox">
                                    <input type="checkbox" :value="record[primaryKey]" v-model="selectedRecords">
                                </td>

                                <template v-for="column in columns">
                                    <td :class="[getColumnClass(column)]">
                                        <div
                                            v-if="!record[column].route"
                                            class="list-table-content clearfix"
                                            v-html="record[column]"
                                        ></div>

                                        <div v-else>
                                            <router-link
                                                :to="record[column].route"
                                                :class="record[column].classNames"
                                                v-html="record[column].text"
                                            ></router-link>
                                        </div>

                                        <div v-if="(showRowActionIn === column) && rowActions.length" class="row-actions">
                                            <template v-for="rowAction in rowActions" v-if="showRowAction(rowAction, tableData.data[recordIndex])">
                                                <template v-if="rowAction.route">
                                                    <router-link
                                                        :to="rowActionRoute(rowAction.route, tableData.data[recordIndex])"
                                                        :class="rowAction.classNames"
                                                        tag="span"
                                                    >
                                                        <a href="#" v-text="rowAction.title"></a>
                                                    </router-link>
                                                </template>

                                                <template v-else>
                                                    <span :class="rowAction.classNames">
                                                        <a
                                                            :href="`#${rowAction.action}`"
                                                            @click.prevent="onClickRowAction(rowAction, tableData.data[recordIndex])"
                                                            v-text="rowAction.title"
                                                        ></a>
                                                    </span>
                                                </template>
                                            </template>
                                        </div>
                                    </td>
                                </template>
                            </tr>
                        </template>

                        <template v-else>
                            <tr>
                                <td :colspan="columns.length + 1">
                                    <slot name="no-data-found">
                                        <span>{{ i18n.noDataFound }}</span>
                                    </slot>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th class="column-checkbox">
                                <input type="checkbox" :checked="isToggleSelectAllChecked" @change="toggleSelectAllItems">
                            </th>

                            <template v-for="column in columns">
                                <th
                                    v-if="!sortableColumns[column]"
                                    :class="columnTitles[column].classNames"
                                    v-html="columnTitles[column].title"
                                ></th>

                                <th
                                    v-else
                                    :class="columnTitles[column].classNames"
                                >
                                    <router-link :to="columnTitles[column].route">
                                        <span>{{ columnTitles[column].title }}</span>
                                        <span class="sorting-indicator"></span>
                                    </router-link>
                                </th>
                            </template>
                        </tr>
                    </tfoot>

                </table>

                <div class="clearfix">
                    <div class="list-table-filter-dropdowns bottom">
                        <div v-if="bulkActions.length" class="list-table-bulk-action">
                            <select v-model="bulkActionSelected">
                                <option value="">{{ i18n.bulkActions }}</option>
                                <option v-for="action in bulkActions" :value="action.name">{{ action.title }}</option>
                            </select>

                            <button type="button" class="button" @click="triggerBulkAction">{{ i18n.apply }}</button>
                        </div>
                    </div>

                    <div class="float-right">
                        <div class="list-table-pagination bottom">
                            <ul class="list-inline">
                                <li v-if="pagination.total" class="list-inline-item total-items">
                                    {{ pagination.total }} {{ i18n.items }}
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    :class="['list-inline-item', pagination.currentPage === 1 ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.firstPageRoute">«</router-link>
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    class="list-inline-item"
                                    :class="['list-inline-item', pagination.currentPage === 1 ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.prevPageRoute">‹</router-link>
                                </li>
                                <li v-if="pagination.hasPagination" class="list-inline-item">
                                    <input
                                        type="text"
                                        :style="{width: currentPageNumberInputWidth}"
                                        v-model="currentPageNumber"
                                    >
                                </li>
                                <li v-if="pagination.hasPagination" class="list-inline-item">
                                    of {{ pagination.lastPage }}
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    :class="['list-inline-item', pagination.currentPage === pagination.lastPage ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.nextPageRoute">›</router-link>
                                </li>
                                <li
                                    v-if="pagination.hasPagination"
                                    :class="['list-inline-item', pagination.currentPage === pagination.lastPage ? 'disabled' : '']"
                                >
                                    <router-link :to="pagination.lastPageRoute">»</router-link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: false,
                default: ''
            },

            i18n: {
                type: Object,
                required: true
            },

            filterMenu: {
                type: Array,
                required: false,
                default() {
                    return [];
                }
            },

            bulkActions: {
                type: Array,
                required: false,
                default() {
                    return [];
                }
            },

            tableData: {
                type: Object,
                required: true
            },

            columns: {
                type: Array,
                required: true
            },

            sortableColumns: {
                type: Object,
                required: false,
                default() {
                    return {};
                }
            },

            primaryKey: {
                type: String,
                required: false,
                default: 'id'
            },

            hideCb: {
                type: Boolean,
                required: false,
                default: false
            },

            rowActions: {
                type: Array,
                required: false,
                default() {
                    return [];
                }
            },

            showRowActionIn: {
                type: String,
                required: false,
                default: 'name'
            }
        },

        data() {
            return {
                currentRoute: {},
                selectedRecords: [],
                isToggleSelectAllChecked: false,
                bulkActionSelected: '',
                currentPageNumber: 1
            };
        },

        computed: {
            records() {
                const vm = this;

                if (!vm.tableData.data.length) {
                    return [];
                }

                return vm.tableData.data.map((record) => {
                    const data = {};

                    data[vm.primaryKey] = record[vm.primaryKey];

                    vm.columns.forEach((column) => {
                        const methodName = _.camelCase(`column-${column}`);

                        if (typeof vm.$parent[methodName] === 'function') {
                            return data[column] = vm.$parent[methodName](record);
                        }

                        const columnName = _.snakeCase(column);

                        return data[column] = ((record[columnName] !== undefined) && (record[columnName] !== null))
                            ? record[columnName]
                            : '&mdash;';
                    });

                    return data;
                });
            },

            wrapperClassName() {
                return this.name ? `wemail-list-table-${this.name}` : '';
            },

            bulkActionOptions() {
                const vm = this;

                return vm.bulkActions.filter((action) => {
                    if (!action.hasOwnProperty('showIf')) {
                        return true;
                    } else if (typeof vm.$parent[action.showIf] === 'function') {
                        return vm.$parent[action.showIf](vm.currentRoute);
                    }

                    return true;
                });
            },

            pagination() {
                const firstPage = 1;
                const lastPage = this.tableData.last_page;

                let nextPage = 1;
                let prevPage = 1;

                let match = this.tableData.next_page_url ? this.tableData.next_page_url.match(/page=(\d+)/) : nextPage;

                if (match && match[1]) {
                    nextPage = parseInt(match[1], 10) || nextPage;
                }

                match = this.tableData.prev_page_url ? this.tableData.prev_page_url.match(/page=(\d+)/) : prevPage;

                if (match && match[1]) {
                    prevPage = parseInt(match[1], 10) || prevPage;
                }

                const route = {
                    name: this.currentRoute.name,
                    params: this.currentRoute.params,
                    query: {
                        page: 1
                    }
                };

                const firstPageRoute = $.extend(true, {}, route);
                const lastPageRoute = $.extend(true, {}, route);
                const nextPageRoute = $.extend(true, {}, route);
                const prevPageRoute = $.extend(true, {}, route);

                firstPageRoute.query.page = undefined;
                lastPageRoute.query.page = lastPage;
                nextPageRoute.query.page = nextPage;
                prevPageRoute.query.page = (prevPage === 1) ? undefined : prevPage;

                let hasPagination = false;

                if (this.tableData.next_page_url || this.tableData.prev_page_url) {
                    hasPagination = true;
                }

                return {
                    currentPage: this.tableData.current_page,
                    total: this.tableData.total,
                    hasPagination,
                    firstPage,
                    lastPage,
                    nextPage,
                    prevPage,
                    firstPageRoute,
                    lastPageRoute,
                    nextPageRoute,
                    prevPageRoute
                };
            },

            currentPageNumberInputWidth() {
                const pageNumber = parseInt(this.currentPageNumber, 10);
                const length = pageNumber.toString().length;

                const LENGTH_PER_DIGIT = 10;
                const MAX_WIDTH = 50;
                let width = 22;

                if (!isNaN(pageNumber) && length > 1) {
                    width = (length * LENGTH_PER_DIGIT) + LENGTH_PER_DIGIT;
                }

                if (width > MAX_WIDTH) {
                    width = MAX_WIDTH;
                }

                return `${width}px`;
            },

            columnTitles() {
                const vm = this;
                const titles = {};

                vm.columns.forEach((column) => {
                    titles[column] = {
                        title: vm.i18n[column],
                        classNames: [vm.getColumnClass(column)]
                    };

                    if (vm.sortableColumns[column]) {
                        const currentRoute = $.extend(true, {}, vm.currentRoute);

                        const route = {
                            name: currentRoute.name,
                            params: currentRoute.params,
                            query: currentRoute.query
                        };

                        let classNames = ['sortable'];

                        if (route.query.orderby === vm.sortableColumns[column]) {
                            route.query.order = (route.query.order === 'asc') ? 'desc' : 'asc';

                            classNames = ['sorted'];
                        } else {
                            route.query.order = 'asc';
                        }

                        classNames.push(route.query.order === 'asc' ? 'desc' : 'asc');

                        route.query.orderby = vm.sortableColumns[column];

                        classNames.push(vm.getColumnClass(column));

                        titles[column] = {
                            title: vm.i18n[column],
                            route,
                            classNames
                        };
                    }
                });

                return titles;
            }
        },

        created() {
            this.setRouteQuery();
        },

        watch: {
            '$route.query': 'setRouteQuery',
            isToggleSelectAllChecked: 'onToggleSelectAllItems',
            selectedRecords: 'onChangeSelectedRecords',
            currentPageNumber: 'onChangeCurrentPageNumber',
            records: {
                deep: true,
                handler: 'onChangeRecords'
            }
        },

        methods: {
            setRouteQuery() {
                this.currentRoute = this.$router.currentRoute;
                this.currentPageNumber = this.currentRoute.query.page || 1;
            },

            onChangeRecords() {
                this.isToggleSelectAllChecked = false;
                this.selectedRecords = [];
                this.bulkActionSelected = '';
            },

            // for some reason exact match for route link doesn't work,
            // so we have this workaround
            filterMenuClass(route) {
                let className = '';

                const routeParams = $.extend(true, {}, route.params);
                const currentRouteParams = $.extend(true, {}, this.currentRoute.params);

                if (route.name === this.currentRoute.name && _.isEqual(routeParams, currentRouteParams)) {
                    className = 'active';
                }

                return className;
            },

            onChangeCurrentPageNumber(pageNumber) {
                pageNumber = parseInt(pageNumber, 10);

                if (pageNumber && (typeof this.currentPageNumber !== 'number')) {
                    this.currentPageNumber = pageNumber;

                    this.$router.push({
                        query: {
                            page: (this.currentPageNumber === 1) ? undefined : this.currentPageNumber
                        }
                    });
                }
            },

            toggleSelectAllItems() {
                this.isToggleSelectAllChecked = !this.isToggleSelectAllChecked;
            },

            onToggleSelectAllItems(isSelected) {
                const vm = this;

                if (isSelected) {
                    vm.selectedRecords = vm.records.map((data) => {
                        return data[vm.primaryKey];
                    });
                } else if (vm.selectedRecords.length === vm.records.length) {
                    vm.selectedRecords =  [];
                }
            },

            onChangeSelectedRecords(records) {
                if (records.length !== this.records.length) {
                    this.isToggleSelectAllChecked = false;
                }
            },

            triggerBulkAction() {
                this.$emit('bulk-action', this.bulkActionSelected, this.selectedRecords);
            },

            showRowAction(rowAction, record) {
                return !rowAction.hasOwnProperty('showIf') || this.$parent[rowAction.showIf](record, this.currentRoute);
            },

            rowActionRoute(route, data) {
                if (typeof this.$parent[route] === 'function') {
                    return this.$parent[route](data);
                }

                return {};
            },

            onClickRowAction(rowAction, data) {
                if (rowAction.onClick && (typeof this.$parent[rowAction.onClick] === 'function')) {
                    this.$parent[rowAction.onClick](data);
                }
            },

            getColumnClass(column) {
                return `column-${_.kebabCase(column)}`;
            }
        }
    };
</script>

<style lang="scss">
    .wemail-list-table {
        position: relative;
        margin-top: 15px;

        .list-sub.filter-menu {
            margin-bottom: 12px;
        }

        .list-table-filter-dropdowns {
            display: inline-block;

            &.bottom {
                margin-top: 3px;
            }
        }

        .list-table-search {
            float: right;
            margin: -7px 0 0;

            input[type="search"] {
                width: 200px;
            }
        }

        .list-table-bulk-action {
            margin-right: 10px;
            margin-bottom: 3px;
        }

        .list-table-pagination {

            .list-inline {
                margin: 0;

                li {
                    margin-right: 1px;
                    margin-bottom: 3px;

                    &.total-items {
                        line-height: 2;
                    }

                    a {
                        display: inline-block;
                        padding: 1px 9px;
                        line-height: 1.8;
                        text-decoration: none;
                        background-color: $wp-border-color;
                        border: 1px solid $wp-border-color-darken;
                    }

                    input[type="text"] {
                        display: inline-block;
                        width: auto;
                        font-size: 13px;
                    }

                    &.disabled {

                        a {
                            color: #a0a5aa;
                            pointer-events: none;
                            background-color: #f7f7f7;
                            border-color: $wp-input-border-color;
                        }
                    }
                }
            }

            &.bottom {

                li {
                    margin-top: 3px;
                }
            }
        }

        .column-checkbox {
            width: 2.2em;
        }

        thead,
        tfoot {

            .column-checkbox {
                padding-top: 2px;
                padding-bottom: 0;
                padding-left: 3px;
                vertical-align: middle;
            }
        }

        tbody {

            .column-checkbox {
                padding-top: 7px;
                padding-left: 10px;
                vertical-align: top;
            }
        }

        .list-table-title {
            font-size: 14px;
            font-weight: 500;
        }

        .column-created-at {
            width: 150px;
        }

        .row-actions {

            & > span {

                &:after {
                    position: relative;
                    top: -1px;
                    display: inline-block;
                    padding: 0 4px;
                    font-size: 12px;
                    color: $wp-input-border-color;
                    content: "|";
                }

                &:last-child {

                    &:after {
                        content: "";
                    }
                }
            }
        }
    }
</style>
