<template>
    <div class="content-settings-container settings-divider">
        <div v-if="settingsTab === 'content'" class="settings-divider-content">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.dividerType }}
                </h4>
                <div class="property">
                    <div class="button-group button-group-extra-padding text-center d-block">
                        <button
                            :class="['button', content.dividerType === 'line' ? 'active' : '']"
                            @click="content.dividerType = 'line'"
                        >{{ i18n.line }}</button>

                        <button
                            :class="['button', content.dividerType === 'image' ? 'active' : '']"
                            @click="content.dividerType = 'image'"
                        >{{ i18n.image }}</button>
                    </div>
                </div>
            </div>

            <template v-if="content.dividerType === 'line'">
                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.height }}
                        <span class="property-value">{{ style.borderTopWidth ? style.borderTopWidth : '0px' }}</span>
                    </h4>
                    <div class="property">
                        <input-range v-model="lineHeight" min="1" max="5"></input-range>
                    </div>
                </div>

                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.width }}
                        <span class="property-value">{{ style.width ? style.width : '0px' }}</span>
                    </h4>
                    <div class="property">
                        <input-range v-model="lineWidth" min="1" max="600"></input-range>
                    </div>
                </div>

                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.color }}
                        <span class="property-value">{{ style.borderTopColor ? style.borderTopColor : '######' }}</span>
                    </h4>
                    <div class="property">
                        <color-picker v-model="style.borderTopColor"></color-picker>
                    </div>
                </div>

                <div class="control-property">
                    <h4 class="property-title clearfix">
                        {{ i18n.style }}
                    </h4>
                    <div class="property">
                        <ul class="border-style-switcher">
                            <li v-for="borderStyle in borderStyles" :class="[style.borderTopStyle === borderStyle ? 'active': '']">
                                <a href="#switch" @click.prevent="content.style.borderTopStyle = borderStyle">
                                    {{ upperFirst(borderStyle) }} <span :style="{borderTopStyle: borderStyle}">&nbsp;</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
            <template v-else>
                <div v-if="!displayGallery" class="control-divider-image">
                    <div class="control-property">
                        <h4 class="property-title clearfix">
                            {{ i18n.image }}

                            <ul class="list-inline-dots property-value">
                                <li>
                                    <a href="#gallery" @click.prevent="showGallery">{{ i18n.gallery }}</a>
                                </li>
                                <li>
                                    <a href="#upload" @click.prevent="browseImage">{{ i18n.browse }}</a>
                                </li>
                            </ul>
                        </h4>
                        <div class="property">
                            <img :src="previewImage" alt="">
                        </div>
                    </div>

                    <div class="control-property">
                        <h4 class="property-title clearfix">
                            {{ i18n.width }}
                            <span class="property-value">{{ content.image.style.width ? content.image.style.width : '0px' }}</span>
                        </h4>
                        <div class="property">
                            <input-range v-model="imageWidth" min="1" max="600"></input-range>
                        </div>
                    </div>

                    <div class="control-property">
                        <h4 class="property-title clearfix">
                            {{ i18n.height }}
                            <span class="property-value">{{ content.image.style.height ? content.image.style.height : '0px' }}</span>
                        </h4>
                        <div class="property">
                            <input-range v-model="imageHeight" min="1" max="100"></input-range>
                        </div>
                    </div>

                </div>
                <div  v-else class="control-property control-divider-image">
                    <h4 class="property-title clearfix">
                        {{ i18n.chooseDivider }}

                        <ul class="list-inline-dots property-value">
                            <li>
                                <a href="#gallery" @click.prevent="hideGallery">{{ i18n.cancel }}</a>
                            </li>
                        </ul>
                    </h4>
                    <div class="property">
                        <ul class="list-image">
                            <li v-for="(image, index) in customizer.dividers.images">
                                <a href="#select"
                                    @click.prevent="selectPresetDivider(image)"
                                    @mouseover.prevent="setTempDivider(image)"
                                    @mouseout.prevent="resetTempDivider"
                                >
                                    <img :src="presetImages[index]">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
        </div>

        <div v-if="settingsTab === 'settings'">
            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.backgroundColor }}
                    <span class="property-value">{{ content.containerStyle.backgroundColor ? content.containerStyle.backgroundColor : '######' }}</span>
                </h4>
                <div class="property">
                    <color-picker v-model="content.containerStyle.backgroundColor"></color-picker>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.containerPadding }}
                    <span class="property-value">{{ content.containerStyle.padding ? content.containerStyle.padding : '0px' }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="containerPadding" min="1" max="100"></input-range>
                </div>
            </div>

            <div class="control-property">
                <h4 class="property-title clearfix">
                    {{ i18n.containerBottomMargin }}
                    <span class="property-value">{{ content.containerStyle.marginBottom ? content.containerStyle.marginBottom : '0px' }}</span>
                </h4>
                <div class="property">
                    <input-range v-model="containerMarginBottom"></input-range>
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

            i18n: {
                type: Object,
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
                displayGallery: false,
                textAligns: ['left', 'center', 'right'],
                borderStyles: ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge'],
                tempCurrentImage: {},
                fileFrame: null
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            lineHeight: {
                get() {
                    return parseInt(this.style.borderTopWidth, 10);
                },

                set(value) {
                    this.style.borderTopWidth = `${value}px`;
                }
            },

            lineWidth: {
                get() {
                    return parseInt(this.style.width, 10);
                },

                set(value) {
                    this.style.width = `${value}px`;
                }
            },

            imageWidth: {
                get() {
                    return parseInt(this.content.image.style.width, 10);
                },

                set(value) {
                    this.content.image.style.width = `${value}px`;
                }
            },

            imageHeight: {
                get() {
                    return parseInt(this.content.image.style.height, 10);
                },

                set(value) {
                    this.content.image.style.height = `${value}px`;
                }
            },

            previewImage() {
                let image = this.content.image.image;

                if (!image) {
                    const firstImage = this.customizer.dividers.images[0].name;

                    image = this.customizer.dividers.baseURL + firstImage;

                    this.content.image.style.height = this.customizer.dividers.images[0].height;
                }

                return image;
            },

            presetImages() {
                const images = [];
                let i = 0;

                for (i = 0; i < this.customizer.dividers.images.length; i++) {

                    images[i] = this.customizer.dividers.baseURL + this.customizer.dividers.images[i].name;
                }

                return images;
            },

            containerPadding: {
                get() {
                    return parseInt(this.content.containerStyle.padding, 10);
                },

                set(value) {
                    this.content.containerStyle.padding = `${value}px`;
                }
            },

            containerMarginBottom: {
                get() {
                    return parseInt(this.content.containerStyle.marginBottom, 10);
                },

                set(value) {
                    this.content.containerStyle.marginBottom = `${value}px`;
                }
            }
        },

        methods: {
            upperFirst(string) {
                return weMail._.upperFirst(string);
            },

            showGallery() {
                this.displayGallery = true;
            },

            hideGallery() {
                this.displayGallery = false;
            },

            selectPresetDivider(image) {
                this.content.image.image = this.customizer.dividers.baseURL + image.name;
                this.content.image.style.height = image.height;
                this.content.image.style.width = '600px';

                this.displayGallery = false;
            },

            setTempDivider(image) {
                this.tempCurrentImage = $.extend(true, {}, this.content.image);

                this.content.image.image = this.customizer.dividers.baseURL + image.name;
                this.content.image.style.height = image.height;
                this.content.image.style.width = '600px';
            },

            resetTempDivider() {
                this.content.image = $.extend(true, {}, this.tempCurrentImage);

                this.tempCurrentImage = {};
            },

            browseImage() {
                const vm = this;
                const selectedFile = {
                    id: 0,
                    url: '',
                    type: ''
                };

                if (vm.fileFrame) {
                    vm.fileFrame.open();
                    return;
                }

                const fileStates = [
                    new wp.media.controller.Library({
                        library: wp.media.query(),
                        multiple: false,
                        title: vm.i18n.selectAnImage,
                        priority: 20,
                        filterable: 'uploaded'
                    })
                ];

                vm.fileFrame = wp.media({
                    title: vm.i18n.selectAnImage,
                    library: {
                        type: ''
                    },
                    button: {
                        text: vm.i18n.selectAnImage
                    },
                    multiple: false,
                    states: fileStates
                });

                vm.fileFrame.on('select', () => {
                    const selection = vm.fileFrame.state().get('selection');

                    selection.map((image) => {
                        image = image.toJSON();

                        if (image.id) {
                            selectedFile.id = image.id;
                        }

                        if (image.url) {
                            selectedFile.url = image.url;
                        }

                        if (image.type) {
                            selectedFile.type = image.type;
                        }

                        if (image.alt) {
                            selectedFile.alt = image.alt;
                        }

                        if (image.title) {
                            selectedFile.title = image.title;
                        }

                        if (image.height) {
                            selectedFile.height = image.height;
                        }

                        console.log(image);

                        vm.insertImage(selectedFile);

                        return null;
                    });
                });

                vm.fileFrame.on('ready', () => {
                    vm.fileFrame.uploader.options.uploader.params = {
                        type: 'wemail-image-uploader'
                    };
                });

                vm.fileFrame.open();
            },

            insertImage(image) {
                if (!image.id || (image.type !== 'image')) {
                    this.alert({
                        type: 'error',
                        text: this.i18n.pleaseSelectAnImage
                    });

                    return;
                }

                const height = parseInt(image.height, 10);
                const MAX_HEIGHT = 100;

                this.content.image.image = image.url;
                this.content.image.style.height = height <= MAX_HEIGHT ? `${height}px` : '100px';
                this.content.image.style.width = '600px';

                this.fileFrame = null;
            }
        }
    };
</script>

<style lang="scss">
    .border-style-switcher {
        padding: 0;
        margin: 0;

        li {
            margin-bottom: 0;

            a {
                position: relative;
                display: block;
                padding: 8px;
                color: $wp-black;
                text-decoration: none;
                background: #fff;
                border-bottom: 1px solid $wp-border-color;

                @include transition();

                &:hover {
                    background: $wp-body-bg;
                }

                &:focus {
                    outline: 0;
                    box-shadow: none;
                }

                span {
                    position: absolute;
                    top: 14px;
                    right: 8px;
                    float: right;
                    width: 200px;
                    height: 8px;
                    border-color: #eaeaea;
                    border-width: 8px;
                }
            }

            &.active a {
                color: #fff;
                background: $wp-black;

                span {
                    border-color: #fff;
                }
            }

            &:last-child a {
                border-bottom: 0;
            }
        }
    }

    .control-divider-image {

        a {
            display: block;
            padding: 5px 0;
            font-weight: 400;
            text-decoration: none;
        }

        .property {
            line-height: 0;
            text-align: center;

            img {
                max-width: 100%;
            }
        }

        .list-inline-dots {
            line-height: 0;

            li {

                &:after {
                    top: 8px;
                }

                a {
                    padding: 0;
                    line-height: 1.3;
                }
            }
        }
    }
</style>
