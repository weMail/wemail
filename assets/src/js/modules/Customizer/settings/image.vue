<template>
    <div class="content-settings-container settings-image">
        <div v-show="settingsTab === 'content'" class="settings-image-content">
            <template v-if="!content.images.length">
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
                                    <a href="#upload" @click.prevent="browseImage">
                                        {{ __('Browse Image') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </template>

            <template v-else>
                <div v-for="(image, index) in content.images" class="row">
                    <div class="col-4 no-right-padding image-preview">
                        <img :src="image.src" alt="">
                    </div>
                    <div class="col-8">
                        <div>
                            <h3>
                                <strong>{{ image.alt ? image.alt : __('Untitled') }}</strong>
                            </h3>
                            <ul class="list-inline-dots">
                                <li>
                                    <a href="#replace" @click.prevent="browseImage(index)">
                                        {{ __('Replace') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#link" @click.prevent="image.openAttrEditor = 'link'">
                                        {{ __('Link') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#alt" @click.prevent="image.openAttrEditor = 'alt'">
                                        {{ __('Alt') }}
                                    </a>
                                </li>
                                <li v-if="content.images.length !== 1">
                                    <a href="#remove" @click.prevent="removeImage(index)">{{ __('Remove') }}</a>
                                </li>
                            </ul>

                            <div v-if="image.openAttrEditor === 'link'" class="image-attr-editor">
                                <strong>{{ __('Set image link') }}</strong>
                                <input class="form-control" type="text" v-model="image.link" autofocus>
                                <p><button type="button" class="button button-small" @click="image.openAttrEditor = ''">{{ __('Close') }}</button></p>
                            </div>
                            <div v-if="image.openAttrEditor === 'alt'" class="image-attr-editor" >
                                <strong>{{ __('Set image alt text') }}</strong>
                                <input class="form-control" type="text" v-model="image.alt" autofocus>
                                <p><button type="button" class="button button-small" @click="image.openAttrEditor = ''">{{ __('Close') }}</button></p>
                            </div>
                        </div>
                    </div>
                </div>

                <p v-if="content.images.length === 1">
                    <button class="button" @click="browseImage">{{ __('Add another image') }}</button>
                </p>
            </template>
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
                    {{ __('Padding') }}
                    <span class="property-value">{{ style.paddingLeft }}</span>
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
                fileFrame: null
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            padding: {
                get() {
                    return parseInt(this.style.paddingLeft, 10);
                },

                set(value) {
                    this.style.paddingTop = `${value}px`;
                    this.style.paddingBottom = `${value}px`;
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
            removeImage(index) {
                if (this.content.images.length === 1) {
                    this.alert({
                        type: 'error',
                        text: __('At least one image is required')
                    });

                    return;
                }

                this.content.images.splice(index, 1);
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
                    this.content.images[index].alt = alt || __('Untitled');
                    this.content.images[index].src = image.url;
                } else {
                    this.content.images.push({
                        alt: alt || __('Untitled'),
                        src: image.url,
                        link: '',
                        openAttrEditor: ''
                    });
                }

                this.fileFrame = null;
            }
        }
    };
</script>

<style lang="scss">
    .settings-image {

        .settings-image-content {
            padding: 10px;
        }

        .row {
            margin-bottom: 15px;
        }

        h3 {
            position: relative;
            margin: 0 0 8px;
            font-size: 15px;

            @include text-truncate;

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
    }
</style>
