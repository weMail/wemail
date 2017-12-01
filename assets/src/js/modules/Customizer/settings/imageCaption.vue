<template>
    <div class="content-settings-container settings-image-captions">
        <div v-show="settingsTab === 'content'" class="settings-image-content">
            <ul class="list-inline settings-tab-list clearfix">
                <li :class="['list-inline-item', (listTab === 1) ? 'active' : '']">
                    <a href="#" @click.prevent="setListTab(1)">{{ __('Caption 1') }}</a>
                </li>
                <li :class="['list-inline-item', (listTab === 2) ? 'active' : '']">
                    <a href="#" :class="[!content.twoCaptions ? 'disabled' : '']" @click.prevent="setListTab(2)">{{ __('Caption 2') }}</a>
                </li>
                <li class="list-inline-item float-right">
                    <label>
                        <input type="checkbox" v-model="content.twoCaptions"> {{ __('Two Captions') }}
                    </label>
                </li>
            </ul>

            <template v-for="(caption, index) in content.captions">
                <div v-if="listTab === (index + 1)" class="settings-image-caption-image">
                        <template v-if="!caption.image.src">
                            <div class="row">
                                <div class="col-4 no-right-padding image-preview">
                                    <img :src="customizer.placeholderImage" alt="">
                                </div>
                                <div class="col-8">
                                    <div>
                                        <h3>
                                            <strong>{{ __('Upload an image') }}</strong>
                                        </h3>
                                        <ul class="list-inline-dots">
                                            <li>
                                                <a href="#upload" @click.prevent="browseImage(index)">
                                                    {{ __('Browse Image') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="row">
                                <div class="col-4 no-right-padding image-preview">
                                    <img :src="caption.image.src" alt="">
                                </div>

                                <div class="col-8">
                                    <div>
                                        <h3>
                                            <strong>{{ caption.image.alt ? caption.image.alt : __('Untitled') }}</strong>
                                        </h3>
                                        <ul class="list-inline-dots">
                                            <li>
                                                <a href="#replace" @click.prevent="browseImage(index)">
                                                    {{ __('Replace') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#link" @click.prevent="caption.image.openAttrEditor = 'link'">
                                                    {{ __('Link') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#alt" @click.prevent="caption.image.openAttrEditor = 'alt'">
                                                    {{ __('Alt') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#remove" @click.prevent="removeImage(index)">{{ __('Remove') }}</a>
                                            </li>
                                        </ul>

                                        <div v-if="caption.image.openAttrEditor === 'link'" class="image-attr-editor">
                                            <strong>{{ __('Set image link') }}</strong>
                                            <input class="form-control" type="text" v-model="caption.image.link" autofocus>
                                            <p><button type="button" class="button button-small" @click="caption.image.openAttrEditor = ''">{{ __('Close') }}</button></p>
                                        </div>
                                        <div v-if="caption.image.openAttrEditor === 'alt'" class="image-attr-editor" >
                                            <strong>{{ __('Set image alt text') }}</strong>
                                            <input class="form-control" type="text" v-model="caption.image.alt" autofocus>
                                            <p><button type="button" class="button button-small" @click="caption.image.openAttrEditor = ''">{{ __('Close') }}</button></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                </div>
            </template>

            <div v-if="displayEditor" class="settings-text-editor">
                <text-editor v-if="listTab === 1" v-model="content.captions[0].text" :shortcodes="customizer.shortcodes"></text-editor>
                <text-editor v-else v-model="content.captions[1].text" :shortcodes="customizer.shortcodes"></text-editor>
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
                    {{ __('Caption Position') }}
                </h4>
                <div class="property">
                    <ul class="list-inline no-margin text-center">
                        <li v-for="position in captionPositions" class="list-inline-item">
                            <label>
                                <input type="radio" :value="position.name" v-model="content.capPosition"> {{ position.title }}
                            </label>
                        </li>
                    </ul>
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
                TWO_CAPTIONS: 2,
                captionPositions: [
                    {
                        name: 'top',
                        title: __('Top')
                    },
                    {
                        name: 'bottom',
                        title: __('Bottom')
                    },
                    {
                        name: 'left',
                        title: __('Left')
                    },
                    {
                        name: 'right',
                        title: __('Right')
                    }
                ]
            };
        },

        computed: {
            style() {
                return this.content.style;
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
            }
        },

        watch: {
            'content.twoCaptions': 'onChangeTwoCaptions'
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

            onChangeTwoCaptions(newVal, oldVal) {
                if (oldVal) {
                    this.setListTab(1);
                }
            },

            browseImage(index) {
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
                        title: __('Select an image'),
                        priority: 20,
                        filterable: 'uploaded'
                    })
                ];

                vm.fileFrame = wp.media({
                    title: __('Select an image'),
                    library: {
                        type: ''
                    },
                    button: {
                        text: __('Select an image')
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

                        vm.insertImage(selectedFile, index);

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

            insertImage(image, index) {
                if (!image.id || (image.type !== 'image')) {
                    this.alert({
                        type: 'error',
                        text: __('Please select an image')
                    });

                    return;
                }

                const alt = image.alt ? image.alt : image.title;

                if (index >= 0) {
                    this.content.captions[index].image.alt = alt || __('Untitled');
                    this.content.captions[index].image.src = image.url;
                }

                this.fileFrame = null;
            },

            removeImage(index) {
                this.content.captions[index].image = {
                    alt: '',
                    src: '',
                    link: '',
                    openAttrEditor: ''
                };
            }
        }
    };
</script>

<style lang="scss">
    .settings-image-captions {

        .settings-image-caption-image {
            padding: 10px;
        }

        h3 {
            position: relative;
            margin: 0 0 8px;
            font-size: 15px;

            @include text-truncate();

            button.button.button-link {
                position: absolute;
                top: 0;
                right: 0;
                height: auto;
                padding: 0 5px;
                font-size: 1em;
                line-height: 1.3;
                color: rgba(68, 68, 68, 0.61);

                &:hover {
                    color: #444;
                }

                &:focus {
                    box-shadow: none;
                }
            }
        }

        .image-preview {

            img {
                width: 100%;
                height: auto;
            }
        }

        .list-inline-dots {
            margin-bottom: 10px;
        }

        .image-attr-editor p {
            margin: 5px 0 0;
        }

        .settings-text-editor {
            border-top: 1px solid $wp-border-color;
        }
    }
</style>
