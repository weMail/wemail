<template>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" :style="containerOuterStyle">
        <tr v-if="content.captions && content.captions.length">
            <td align="center" valign="top" :style="containerInnerStyle">
                <div class="wrapper" :style="wrapperStyle">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" :style="{maxWidth: twoCaptionsSideBySide ? '300px' : '600px'}">
                        <tr>
                            <td align="center" valign="top" :style="wrapperTdStyle[0]">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="leftColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div
                                                            v-if="content.capPosition === 'top'"
                                                            class="wemail-content-text"
                                                            :style="textContainerStyle"
                                                            v-html="content.captions[0].text"
                                                        ></div>

                                                        <div v-if="content.captions[0].image.src" class="wemail-content-image" :style="imageContainerStyle">
                                                            <a v-if="content.captions[0].image.link" :href="content.captions[0].image.link" target="_blank">
                                                                <img :src="content.captions[0].image.src" :style="imageStyle" :alt="content.captions[0].image.alt">
                                                            </a>

                                                            <img v-else :src="content.captions[0].image.src" :style="imageStyle" :alt="content.captions[0].image.alt">
                                                        </div>

                                                        <div v-else class="wemail-content-image no-image-content">
                                                            <img :src="customizer.placeholderImage" style="max-width: 120px; height: auto;" alt="">
                                                        </div>

                                                        <div
                                                            v-if="content.capPosition !== 'top'"
                                                            class="wemail-content-text"
                                                            :style="textContainerStyle"
                                                            v-html="content.captions[0].text"
                                                        ></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div v-if="twoCaptions" class="wrapper" :style="rightWrapperStyle">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" :style="{maxWidth: twoCaptionsSideBySide ? '300px' : '600px'}">
                        <tr>
                            <td align="center" valign="top" :style="wrapperTdStyle[1]">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="rightColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div
                                                            v-if="content.capPosition === 'top'"
                                                            class="wemail-content-text"
                                                            :style="textContainerStyle"
                                                            v-html="content.captions[1].text"
                                                        ></div>

                                                        <div v-if="content.captions[1].image.src" class="wemail-content-image" :style="imageContainerStyle">
                                                            <a v-if="content.captions[1].image.link" :href="content.captions[1].image.link" target="_blank">
                                                                <img :src="content.captions[1].image.src" :style="imageStyle" :alt="content.captions[1].image.alt">
                                                            </a>

                                                            <img v-else :src="content.captions[1].image.src" :style="imageStyle" :alt="content.captions[1].image.alt">
                                                        </div>

                                                        <div v-else class="wemail-content-image no-image-content">
                                                            <img :src="customizer.placeholderImage" style="max-width: 120px; height: auto;" alt="">
                                                        </div>

                                                        <div
                                                            v-if="content.capPosition !== 'top'"
                                                            class="wemail-content-text"
                                                            :style="textContainerStyle"
                                                            v-html="content.captions[1].text"
                                                        ></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</template>

<script>
    export default {
        props: {
            content: {
                type: Object,
                required: true
            },

            globalCss: {
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
                TWO_CAPTIONS: 2,
                FULL_WIDTH: 600,
                HALF_WIDTH: 300
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            twoCaptions() {
                return this.content.twoCaptions;
            },

            twoCaptionsSideBySide() {
                return !(this.content.capPosition === 'left' || this.content.capPosition === 'right') && this.twoCaptions;
            },

            hasBorder() {
                return parseInt(this.style.borderWidth, 10);
            },

            containerOuterStyle() {
                return {
                    maxWidth: '600px',
                    marginBottom: this.style.marginBottom
                };
            },

            containerInnerStyle() {
                return {
                    fontSize: 0,
                    color: this.style.color
                };
            },

            centerPadding() {
                if (!this.twoCaptionsSideBySide) {
                    return this.style.padding;
                }

                const padding = Math.floor(parseInt(this.style.padding, 10) / this.TWO_CAPTIONS);

                return `${padding}px`;
            },

            leftColumnPaddings() {
                return {
                    backgroundColor: this.style.backgroundColor,
                    paddingTop: this.style.padding,
                    paddingRight: this.hasBorder ? this.style.padding : this.centerPadding,
                    paddingBottom: this.style.padding,
                    paddingLeft: this.style.padding,
                    borderWidth: this.style.borderWidth,
                    borderStyle: 'solid',
                    borderColor: this.style.borderColor
                };
            },

            rightColumnPaddings() {
                return {
                    backgroundColor: this.style.backgroundColor,
                    paddingTop: this.style.padding,
                    paddingRight: this.style.padding,
                    paddingBottom: this.style.padding,
                    paddingLeft: this.hasBorder ? this.style.padding : this.centerPadding,
                    borderWidth: this.style.borderWidth,
                    borderStyle: 'solid',
                    borderColor: this.style.borderColor
                };
            },

            wrapperStyle() {
                const minWidth = this.HALF_WIDTH - parseInt(this.style.borderWidth, 10);

                return {
                    display: 'inline-block',
                    maxWidth: this.twoCaptionsSideBySide ? '50%' : '100%',
                    minWidth: `${minWidth}px`,
                    verticalAlign: 'top',
                    width: '100%'
                };
            },

            wrapperTdStyle() {
                const padding = (parseInt(this.style.borderWidth, 10) && this.twoCaptions) ? this.centerPadding : 0;

                return [
                    {
                        paddingRight: padding
                    },
                    {
                        paddingLeft: padding
                    }
                ];
            },

            rightWrapperStyle() {
                return $.extend(true, {
                    float: 'right'
                }, this.wrapperStyle);
            },

            imageContainerStyle() {
                const style = {};

                if (this.content.capPosition === 'left') {
                    style.width = '50%';
                    style.float = 'right';
                } else if (this.content.capPosition === 'right') {
                    style.width = '50%';
                    style.float = 'left';
                }

                return style;
            },

            imageStyle() {
                const padding = parseInt(this.style.padding, 10);
                const border = parseInt(this.style.borderWidth, 10);

                let width = 0;

                if (this.twoCaptions) {
                    if (border) {
                        width = this.HALF_WIDTH - (padding / this.TWO_CAPTIONS) - (this.TWO_CAPTIONS * border) - (this.TWO_CAPTIONS * padding);
                    } else {
                        width = this.HALF_WIDTH - (padding + (padding / this.TWO_CAPTIONS));
                    }
                } else {
                    width = this.FULL_WIDTH - (this.TWO_CAPTIONS * padding) - border;
                }

                width = Math.floor(width);
                width = `${width}px`;

                if (this.content.capPosition === 'left' || this.content.capPosition === 'right') {
                    width = '100%';
                }

                return {
                    maxWidth: width,
                    height: 'auto'
                };
            },

            textContainerStyle() {
                const style = {
                    paddingTop: '0px',
                    paddingRight: '0px',
                    paddingBottom: '0px',
                    paddingLeft: '0px'
                };

                if (this.content.capPosition === 'top') {
                    style.paddingBottom = '8px';
                } else if (this.content.capPosition === 'bottom') {
                    style.paddingTop = '8px';
                } else if (this.content.capPosition === 'left') {
                    style.paddingRight = '8px';
                    style.width = '50%';
                    style.float = 'left';
                } else {
                    style.paddingLeft = '8px';
                    style.width = '50%';
                    style.float = 'right';
                }

                return style;
            }
        }
    };
</script>
