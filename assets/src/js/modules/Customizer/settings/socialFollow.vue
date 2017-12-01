<template>
    <div class="content-settings-container settings-social-follow">
        <div v-show="settingsTab === 'content'" class="settings-social-follow-content">
            <div v-for="(icon, index) in content.icons" class="settings-social-icons">
                <div class="icon-selector clearfix">
                    <div class="selector-icon pull-left">
                        <img :src="imageUrls[icon.site]" alt="">
                    </div>
                    <div class="selector-list pull-left">
                        <div class="row social-network-dropdown">
                            <div class="col-10">
                                <multiselect
                                    v-model="selectedNetworks[index]"
                                    :options="networks"
                                    :id="index"
                                    track-by="name"
                                    label="title"
                                    @select="onSelectNetwork"
                                ></multiselect>
                            </div>
                            <div class="col-2">
                                <a v-if="content.icons.length > 1" href="#remove" class="remove-icon text-center" @click.prevent="removeService(index)">
                                    <i class="fa fa-minus-circle"></i>
                                </a>
                            </div>
                        </div>
                        <p>
                            <label><strong>{{ __('Page Link') }}</strong>
                                <input type="text" class="form-control" v-model="icon.link">
                            </label>
                        </p>
                        <p>
                            <label><strong>{{ __('Link Text') }}</strong>
                                <input type="text" class="form-control" v-model="icon.text">
                            </label>
                        </p>
                    </div>
                </div>
            </div>
            <p>
                <button type="button" class="button button-block" @click="addMoreNetwork">{{ __('Add More Network') }}</button>
            </p>
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
                    {{ __('Font Size') }}
                    <span class="property-value">
                        {{ style.fontSize ? style.fontSize : '0px' }}
                    </span>
                </h4>
                <div class="property">
                    <input-range v-model="fontSize" min="10" max="30"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Uppercase') }}
                </h4>
                <div class="property">
                    <label><input type="checkbox" v-model="upperCase"> {{ __('Yes') }}</label>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Font Weight') }}
                </h4>
                <div class="property">
                    <ul class="list-inline no-margin">
                        <li class="list-inline-item">
                            <label class="fix-radio">
                                <input type="radio" value="normal" v-model="style.fontWeight"> {{ __('Normal') }}
                            </label>
                        </li>
                        <li class="list-inline-item">
                            <label class="fix-radio">
                                <input type="radio" value="bold" v-model="style.fontWeight"> {{ __('Bold') }}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Icon Margin') }}
                    <span class="property-value">
                        {{ iconMargin ? iconMargin + 'px' : '0px' }}
                    </span>
                </h4>
                <div class="property">
                    <input-range v-model="iconMargin" min="10" max="50"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Padding') }}
                    <span class="property-value">{{ style.padding }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="padding"></input-range>
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
                    {{ __('Display') }}
                </h4>
                <div class="property">
                    <ul class="list-inline no-margin text-center">
                        <li v-for="displayType in displayTypes" class="list-inline-item">
                            <label>
                                <input type="radio" :value="displayType.type" v-model="content.display"> {{ displayType.title }}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Align') }}
                </h4>
                <div class="property text-center">
                    <div class="button-group button-group-extra-padding">
                        <button
                            v-for="textAlign in textAligns"
                            type="button"
                            :class="['button', (style.textAlign === textAlign) ? 'active' : '']"
                            @click="style.textAlign = textAlign"
                        >
                            <i :class="['fa fa-align-' + textAlign]"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ __('Layout') }}
                </h4>

                <div class="property">
                    <div v-for="(layout, name) in layouts" class="social-icon-layouts">
                        <div v-if="content.display === name" class="row no-gutters">
                            <div v-for="type in layout" :class="['col-6', 'layout-' + name]">
                                <a
                                    href="#"
                                    :class="[
                                        type.layout + '-' + type.size,
                                        (content.layout === type.layout && content.size === type.size) ? 'active' : ''
                                    ]"
                                    @click.prevent="switchLayout(type.layout, type.size)"
                                ><span>&nbsp;</span></a>
                            </div>
                        </div>
                    </div>
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
                displayTypes: [
                    {
                        type: 'icon',
                        title: __('Icon Only')
                    },
                    {
                        type: 'text',
                        title: __('Text Only')
                    },
                    {
                        type: 'both',
                        title: __('Both')
                    }
                ],
                textAligns: ['left', 'center', 'right'],
                layouts: {
                    icon: [
                        {
                            layout: 'horizontal',
                            size: 'default'
                        },
                        {
                            layout: 'vertical',
                            size: 'default'
                        },
                        {
                            layout: 'horizontal',
                            size: 'large'
                        },
                        {
                            layout: 'vertical',
                            size: 'large'
                        }
                    ],

                    text: [
                        {
                            layout: 'horizontal',
                            size: 'default'
                        },
                        {
                            layout: 'vertical',
                            size: 'default'
                        }
                    ],

                    both: [
                        {
                            layout: 'horizontal',
                            size: 'default'
                        },
                        {
                            layout: 'vertical',
                            size: 'default'
                        },
                        {
                            layout: 'horizontal',
                            size: 'large'
                        },
                        {
                            layout: 'vertical',
                            size: 'large'
                        }
                    ]
                }
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            fontSize: {
                get() {
                    return parseInt(this.style.fontSize, 10);
                },

                set(value) {
                    this.style.fontSize = `${value}px`;
                }
            },

            upperCase: {
                get() {
                    return this.style.textTransform === 'uppercase' || false;
                },

                set(isUpperCase) {
                    this.style.textTransform = isUpperCase ? 'uppercase' : 'none';
                }
            },

            iconMargin: {
                get() {
                    return parseInt(this.content.iconMargin, 10);
                },

                set(value) {
                    this.content.iconMargin = `${value}px`;
                }
            },

            padding: {
                get() {
                    return parseInt(this.style.padding, 10);
                },

                set(value) {
                    this.style.padding = `${value}px`;
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
            },

            imageUrls() {
                const vm = this;
                const urls = {};

                this.content.icons.forEach((icon) => {
                    urls[icon.site] = `${weMail.assetsURL}/images/social-icons/${vm.content.iconStyle}-${icon.site}.png`;
                });

                return urls;
            },

            networks() {
                const vm = this;

                return this.customizer.socialNetworks.networks.map((network) => {
                    return {
                        name: network,
                        title: vm.customizer.socialNetworks.title[network]
                    };
                });
            },

            selectedNetworks() {
                const vm = this;

                return this.content.icons.map((icon) => {
                    return {
                        name: icon.site,
                        title: vm.customizer.socialNetworks.title[icon.site] || __('Website')
                    };
                });
            }
        },

        methods: {
            onSelectNetwork(option, index) {
                this.content.icons[index].site = option.name;
                this.content.icons[index].link = this.customizer.socialNetworks.defaults[option.name];
                this.content.icons[index].text = this.customizer.socialNetworks.title[option.name];
            },

            addMoreNetwork() {
                const newService = {
                    site: 'website',
                    link: this.customizer.socialNetworks.defaults.website,
                    text: __('Website')
                };

                this.content.icons.push(newService);
            },

            removeService(index) {
                this.content.icons.splice(index, 1);
            },

            switchLayout(layout, size) {
                this.content.layout = layout;
                this.content.size = size;
            }
        }
    };
</script>

<style lang="scss">
    .settings-social-icons {
        padding: 10px;
        border-bottom: 1px solid $wp-border-color;

        .icon-selector {
            line-height: 0;

            .selector-icon {
                width: 30px;
                margin-right: 10px;
            }

            .selector-list {
                width: 270px;

                .social-network-dropdown {
                    margin-bottom: 5px;

                    a.remove-icon {
                        display: inline-block;
                        width: 16px;
                        font-size: 18px;
                        line-height: 1.6;
                        color: $wp-black;
                        opacity: 0.5;

                        @include transition;

                        &:hover {
                            opacity: 1;
                        }

                        &:focus {
                            outline: 0;
                            box-shadow: none;
                        }
                    }
                }

                p {
                    margin: 0 0 5px;

                    &:last-child {
                        margin-bottom: 0;
                    }
                }
            }
        }


        img {
            width: 100%;
            height: auto;
        }

        &:last-child {
            border-bottom: 0;
        }

        & + p {
            padding: 10px;
        }
    }

    .social-icon-layouts {
        width: 180px;
        margin: 0 auto;
        text-align: center;

        a {
            display: inline-block;
            margin: 5px 0;
            text-decoration: none;
            box-shadow: none !important;
            opacity: 0.3;

            @include transition;

            span {
                display: block;
                width: 80px;
                height: 80px;
                background-repeat: repeat-y;
            }

            &:hover {
                opacity: 0.8;
            }

            &.active {
                opacity: 1;
            }
        }

        .layout-icon {

            span {
                background-image: url(../images/social-share-layout-icon.png);
            }

            .horizontal-default span {
                background-position: 0 -240px;
            }

            .vertical-default span {
                background-position: 0 -160px;
            }

            .horizontal-large span {
                background-position: 0 -80px;
            }

            .vertical-large span {
                background-position: 0 0;
            }
        }

        .layout-text {

            span {
                background-image: url(../images/social-share-layout-text.png);
            }

            .vertical-default span {
                background-position: 0 0;
            }

            .horizontal-default span {
                background-position: 0 -80px;
            }
        }

        .layout-both {

            span {
                background-image: url(../images/social-share-layout-both.png);
            }

            .horizontal-default span {
                background-position: 0 -240px;
            }

            .vertical-default span {
                background-position: 0 -160px;
            }

            .horizontal-large span {
                background-position: 0 -80px;
            }

            .verticle-large span {
                background-position: 0 0;
            }
        }
    }
</style>

