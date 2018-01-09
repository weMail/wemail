<template>
    <div v-if="isLoaded" class="row settings-life-stages">
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
                                    <i class="fa fa-check-circle"></i> {{ __('default') }}
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
                            {{ __('Name') }}
                            <input type="text" class="form-control" v-model="lifeStage.i18n">
                        </label>

                        <label>
                            {{ __('Key') }}
                            <input type="text" class="form-control" v-model="lifeStage.name">
                            <span class="hint">{{ __('only lowercased a-z, 0-9 and underscore are allowed') }}</span>
                        </label>

                        <label class="strong">
                            <input type="checkbox" v-model="lifeStage.default" :disabled="stage === settings.default">
                            {{ __('Make it the default life stage') }}
                        </label>

                        <div class="item-content-footer">
                            <ul>
                                <li>
                                    <a
                                        href="#"
                                        :class="['text-danger', (stage === settings.default) ? 'disabled' : '']"
                                        @click.prevent="remove"
                                    >{{ __('Remove') }}</a>
                                </li>
                                <li>
                                    <a href="#" @click.prevent="reset">{{ __('Cancel') }}</a>
                                </li>
                                <li class="save-item">
                                    <a
                                        href="#"
                                        class="button button-primary button-small"
                                        @click.prevent="save"
                                    >{{ __('Save') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>

            <button type="button" class="button button-primary button-block" @click="addNewLifeStage">
                <i class="fa fa-plus-circle"></i> {{ __('Add new life stage') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'settingsLifeStages',

        hideSaveButton: true,

        mixins: weMail.getMixins('settings', 'dataValidators'),

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

        watch: {
            'lifeStage.name': 'onChangeName'
        },

        methods: {
            afterLoaded() {
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

                this.saveSettings();
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
                const vm = this;

                if (vm.settings.names.length === 1) {
                    vm.error(__('At least one life stage is required'));
                    return;
                }

                if (vm.lifeStage.default) {
                    vm.error(__('You cannot remove the default life stage'));
                    return;
                }

                vm.warn({
                    title: __('Are you sure you want to remove this life stage?'),
                    text: __('All the subscriber belongs to this stage will be moved to default life stage'),
                    confirmButtonText: __('Yes delete it')
                }).then((deleteIt) => {
                    if (deleteIt) {
                        Vue.delete(vm.settings.i18n, vm.lifeStage.name);
                        Vue.delete(vm.settings.names, vm.lifeStage.index);

                        vm.reset();
                        vm.saveSettings();
                    }
                });

            },

            save() {
                const vm = this;

                if (vm.isEmpty(vm.lifeStage.i18n) || vm.isEmpty(vm.lifeStage.name)) {
                    vm.error(__('Both name and key fields are required'));
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
                this.saveSettings();
            },

            saveSettings() {
                this.$emit('save-settings', true);
            },

            addNewLifeStage() {
                const index = this.settings.names.length;
                const newStageName = `new_life_stage_${moment().unix()}`;

                this.settings.names.splice(index, 1, newStageName);

                this.settings.i18n[newStageName] = __('New life stage');

                this.open(index);

                this.saveSettings();
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

        .ui-sortable-placeholder {
            visibility: visible !important;
            border: 1px dashed $wp-border-color-darken;
        }
    }
</style>
