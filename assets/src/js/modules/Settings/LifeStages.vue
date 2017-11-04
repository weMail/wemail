<template>
    <div v-if="isLoaded" class="row settings-life-stages hide-save-button">
        <div class="col-sm-6 margin-bottom-20">
            <ul id="life-stage-settings-list" class="list-accordion">
                <li
                    v-for="(stage, index) in settings.names"
                    :key="stage"
                    :data-index="index"
                    class="clearfix"
                >
                    <div class="item-header">
                        <span class="item-name">
                            {{ settings.i18n[stage] }}

                            <span v-if="stage === settings.default" class="item-default">
                                <span class="text-muted">-</span>
                                <span class="text-success">
                                    <i class="fa fa-check-circle"></i> {{ i18n.default }}
                                </span>
                            </span>
                        </span>

                        <a
                            v-if="lifeStage.index !== index"
                            href="#"
                            class="item-edit"
                            @click.prevent="open(index)"
                        >
                            <i class="fa fa-caret-down"></i>
                        </a>

                        <a
                            v-else
                            href="#"
                            class="item-edit"
                            @click.prevent="reset"
                        >
                            <i class="fa fa-caret-up"></i>
                        </a>
                    </div>

                    <div v-if="lifeStage.index === index" class="item-content">
                        <label>
                            {{ i18n.name }}
                            <input type="text" class="form-control" v-model="lifeStage.i18n">
                        </label>

                        <label>
                            {{ i18n.key }}
                            <input type="text" class="form-control" v-model="lifeStage.name">
                            <span class="hint">{{ i18n.nameHint }}</span>
                        </label>

                        <label class="strong">
                            <input type="checkbox" v-model="lifeStage.default" :disabled="stage === settings.default">
                            {{ i18n.makeItDefault }}
                        </label>

                        <div class="item-content-footer">
                            <ul>
                                <li>
                                    <a href="#" @click.prevent="remove" class="text-danger">{{ i18n.remove }}</a>
                                </li>
                                <li>
                                    <a href="#" @click.prevent="reset">{{ i18n.cancel }}</a>
                                </li>
                                <li class="save-item">
                                    <a
                                        href="#"
                                        class="button button-primary button-small"
                                        @click.prevent="save"
                                    >{{ i18n.save }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>

            <span class="add-new-life-stage">

            </span>

            <button type="button" class="button button-block" @click="addNewLifeStage">
                <i class="fa fa-plus-circle"></i> {{ i18n.addNewLifeStage }}
            </button>
        </div>

        <div class="col-sm-6">
            <div class="text-warning">Need to hide the save settings button</div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'settingsLifeStages',

        mixins: weMail.getMixins('settings', 'routeComponent', 'dataValidators'),

        data() {
            return {
                lifeStage: {
                    index: -1,
                    i18n: '',
                    name: '',
                    default: false
                }
            };
        },

        computed: {
            ...Vuex.mapState('settingsLifeStages', ['i18n', 'settings'])
        },

        watch: {
            'lifeStage.name': 'onChangeName',
            isLoaded: 'afterLoaded'
        },

        methods: {
            afterLoaded(isLoaded) {
                if (isLoaded) {
                    const vm = this;

                    Vue.nextTick(() => {
                        vm.bindSortable();
                    });
                }
            },

            bindSortable() {
                $('#life-stage-settings-list').sortable({
                    handle: '.item-header',
                    cancel: '.item-edit',
                    stop: this.updateOrder
                }).disableSelection();
            },

            updateOrder(e, ui) {
                const data = ui.item[0].dataset;
                const fromIndex = parseInt(data.index, 10);
                const toIndex = parseInt($(ui.item).index(), 10);

                const lifeStage = this.settings.names.splice(fromIndex, 1);

                this.settings.names.splice(toIndex, 0, lifeStage[0]);
            },

            open(index) {
                const name = this.settings.names[index];
                const i18n = this.settings.i18n[name];
                const isDefault = (this.settings.default === name) || false;

                this.lifeStage = {
                    index,
                    i18n,
                    name,
                    default: isDefault
                };
            },

            reset() {
                this.lifeStage = {
                    index: -1,
                    i18n: '',
                    name: '',
                    default: false
                };
            },

            remove() {
                if (this.settings.names.length === 1) {
                    this.error(this.i18n.atLeastOneMsg);
                    return;
                }

                if (this.lifeStage.default) {
                    this.error(this.i18n.errorMsgDefault);
                    return;
                }

                Vue.delete(this.settings.i18n, this.lifeStage.name);
                Vue.delete(this.settings.names, this.lifeStage.index);

                this.reset();
            },

            save() {
                const vm = this;

                if (vm.isEmpty(vm.lifeStage.i18n) || vm.isEmpty(vm.lifeStage.name)) {
                    vm.error(vm.i18n.requiredMsg);
                    return;
                }

                vm.settings.names.splice(vm.lifeStage.index, 1, vm.lifeStage.name);

                const i18n = {};

                vm.settings.names.forEach((name) => {
                    i18n[name] = (vm.lifeStage.name === name) ? vm.lifeStage.i18n : vm.settings.i18n[name];
                });

                vm.settings.i18n = i18n;

                if (vm.lifeStage.default) {
                    vm.settings.default = vm.lifeStage.name;
                }

                vm.reset();
            },

            addNewLifeStage() {
                const index = this.settings.names.length;
                const newStageName = `new_life_stage_${index}`;

                this.settings.names.splice(index, 1, newStageName);

                this.settings.i18n[newStageName] = 'New life stage';

                this.open(index);
            },

            onChangeName(name) {
                this.lifeStage.name = name.replace(/[^a-z0-9_]+/g, '');
            }
        }
    };
</script>

<style lang="scss">
    .settings-life-stages {

        .item-default {
            font-size: 12px;
        }

        .item-content {

            label {
                margin-bottom: 10px;

                .hint {
                    font-size: 12px;
                    font-style: italic;
                }
            }
        }

        .item-content-footer {
            padding: 4px 10px;
            margin: 0 -10px -10px;
            background: #fafafa;
            border-top: 1px solid $wp-border-color;

            ul {
                margin: 0;

                @include clearfix;

                li {
                    position: relative;
                    display: inline-block;
                    float: left;
                    padding: 0;
                    margin: 0;
                    line-height: 1.9;

                    &:first-child {
                        margin-right: 10px;

                        &:after {
                            position: absolute;
                            top: -1px;
                            right: -7px;
                            content: "|";
                            opacity: 0.4;
                        }

                    }

                    &.save-item {
                        float: right;
                    }

                    a {
                        font-weight: 400;
                        text-decoration: none;
                    }

                }
            }
        }
    }
</style>
