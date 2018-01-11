<template>
    <div class="content-settings-container settings-text">
        <div v-show="settingsTab === 'content'">
            <ul class="list-inline settings-tab-list clearfix">
                <li :class="['list-inline-item', (listTab === 1) ? 'active' : '']">
                    <a href="#" @click.prevent="setListTab(1)">{{ __('Column 1') }}</a>
                </li>
                <li :class="['list-inline-item', (listTab === 2) ? 'active' : '']">
                    <a href="#" :class="[!isTwoColumns ? 'disabled' : '']" @click.prevent="setListTab(2)">{{ __('Column 2') }}</a>
                </li>
                <li class="list-inline-item float-right">
                    <label>
                        <input type="checkbox" v-model="isTwoColumns"> {{ __('Two Columns') }}
                    </label>
                </li>
            </ul>

            <div v-if="displayEditor">
                <text-editor v-if="listTab === 1" v-model="content.texts[0]" :shortcodes="customizer.shortcodes"></text-editor>
                <text-editor v-else v-model="content.texts[1]" :shortcodes="customizer.shortcodes"></text-editor>
            </div>
        </div>

        <div v-show="settingsTab === 'style'">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Background Color') }}
                    <span class="property-value">{{ style.backgroundColor ? style.backgroundColor : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="style.backgroundColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Font Color') }}
                    <span class="property-value">{{ style.color ? style.color : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="style.color"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Padding Top-Bottom') }}
                    <span class="property-value">{{ style.paddingTop }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="paddingTopBottom"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Padding Left-Right') }}
                    <span class="property-value">{{ style.paddingLeft }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="paddingLeftRight"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Border') }}
                    <span class="property-value">
                        {{ style.borderWidth ? style.borderWidth : '0px' }} &nbsp;
                        {{ style.borderColor ? style.borderColor : '######' }}
                    </span>
                </h4>
                <div class="property">
                    <input-range v-model="borderWidth" min="0" max="10"></input-range>
                    <br><br>
                    <color-picker v-model="style.borderColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Bottom Margin') }}
                    <span class="property-value">{{ style.marginBottom }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="marginBottom"></input-range>
                </div>
            </div>
        </div>

        <div v-show="settingsTab === 'settings'">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Column Split') }}
                </h4>
                <div class="property">
                    <ul v-if="isTwoColumns" class="list-inline text-center column-split-list">
                        <li
                            v-for="split in columnSplits"
                            :class="['list-inline-item', content.columnSplit === split ? 'active' : '']"
                        >
                            <a href="#" @click.prevent="setColumnSplit(split)">
                                <span :class="[`column-split split-${split}`]">&nbsp;</span>
                            </a>
                        </li>
                    </ul>
                    <em v-else class="text-muted">{{ __('Column splitting is available when "Two Columns" is set') }}</em>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            settingsTab: {
                type: String,
                required: true
            },

            sectionIndex: {
                type: Number,
                required: true
            },

            contentIndex: {
                type: Number,
                required: true
            },

            content: {
                type: Object,
                required: true
            },

            customizer: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                listTab: 1,
                displayEditor: true,
                columnSplits: ['1-1', '2-1', '1-2']
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            isTwoColumns: {
                get() {
                    return JSON.parse(this.content.twoColumns);
                },

                set(twoColumns) {
                    this.content.twoColumns = twoColumns;

                    if (!twoColumns) {
                        this.setListTab(1);
                        this.content.columnSplit = null;
                    } else {
                        this.content.columnSplit = '1-1';
                    }
                }
            },

            paddingTopBottom: {
                get() {
                    return parseInt(this.style.paddingTop, 10);
                },

                set(value) {
                    this.style.paddingTop = `${value}px`;
                    this.style.paddingBottom = `${value}px`;
                }
            },

            paddingLeftRight: {
                get() {
                    return parseInt(this.style.paddingLeft, 10);
                },

                set(value) {
                    this.style.paddingLeft = `${value}px`;
                    this.style.paddingRight = `${value}px`;
                }
            },

            borderWidth: {
                get() {
                    return parseInt(this.style.borderWidth, 10);
                },

                set(value) {
                    this.style.borderWidth = `${value}px`;
                }
            },

            marginBottom: {
                get() {
                    return parseInt(this.style.marginBottom, 10);
                },

                set(value) {
                    this.style.marginBottom = `${value}px`;
                }
            }
        },

        methods: {
            setListTab(tab) {
                if (tab !== this.listTab) {
                    const vm = this;
                    const TIMEOUT = 400;

                    vm.displayEditor = false;

                    setTimeout(() => {
                        vm.listTab = tab;
                        vm.displayEditor = true;
                    }, TIMEOUT);
                }
            },

            setColumnSplit(split) {
                this.content.columnSplit = split;
            }
        }
    };
</script>

<style lang="scss">
    .column-split-list {
        text-align: center;

        li {
            margin: 0 4px;

            &.active {

                .column-split.split-1-1 {
                    background-position: 0 0;
                }

                .column-split.split-1-2 {
                    background-position: 0 -160px;

                }

                .column-split.split-2-1 {
                    background-position: 0 -319px;

                }
            }
        }

        a {
            display: block;
            text-decoration: none;
        }

        .column-split {
            display: inline-block;
            width: 80px;
            height: 80px;
            background-image: url(../images/column-split.png);
            background-repeat: repeat-y;

            &.split-1-1 {
                background-position: 0 -80px;
            }

            &.split-1-2 {
                background-position: 0 -239px;

            }

            &.split-2-1 {
                background-position: 0 -398px;

            }
        }
    }
</style>
