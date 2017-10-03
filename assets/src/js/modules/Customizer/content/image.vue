<template>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" :style="containerOuterStyle">
        <tr v-if="content.images && content.images.length">
            <td align="center" valign="top">
                <div class="wrapper" :style="wrapperStyle">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" :style="{maxWidth: twoColumns ? '300px' : '600px'}">
                        <tr>
                            <td align="center" valign="top" :style="wrapperTdStyle[0]">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="leftColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-image">
                                                            <a v-if="content.images[0].link" :href="content.images[0].link" target="_blank">
                                                                <img :src="content.images[0].src" :style="imageStyle" :alt="content.images[0].alt">
                                                            </a>

                                                            <img v-else :src="content.images[0].src" :style="imageStyle" :alt="content.images[0].alt">
                                                        </div>
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
                <div v-if="twoColumns" class="wrapper" :style="rightWrapperStyle">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px;">
                        <tr>
                            <td align="center" valign="top" :style="wrapperTdStyle[1]">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="rightColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-image">
                                                            <a v-if="content.images[1].link" :href="content.images[1].link" target="_blank">
                                                                <img :src="content.images[1].src" :style="imageStyle" :alt="content.images[1].alt">
                                                            </a>

                                                            <img v-else :src="content.images[1].src" :style="imageStyle" :alt="content.images[1].alt">
                                                        </div>
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
        <tr v-else>
            <td align="center" valign="top">
                <div :style="wrapperStyle" class="wrapper">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" :style="{maxWidth: twoColumns ? '300px' : '600px'}" class="wrapper">
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="leftColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-image no-image-content">
                                                            <img :src="customizer.placeholderImage" style="max-width: 120px; height: auto;" alt="">
                                                        </div>
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
                TWO_COLUMNS: 2,
                FULL_WIDTH: 600,
                HALF_WIDTH: 300
            };
        },

        computed: {
            style() {
                return this.content.style;
            },

            twoColumns() {
                return this.content.images.length === this.TWO_COLUMNS;
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

            centerPadding() {
                if (!this.twoColumns) {
                    return this.style.paddingRight;
                }

                const padding = Math.floor(parseInt(this.style.paddingRight, 10) / this.TWO_COLUMNS);

                return `${padding}px`;
            },

            leftColumnPaddings() {
                return {
                    backgroundColor: this.style.backgroundColor,
                    paddingTop: this.style.paddingTop,
                    paddingRight: this.hasBorder ? this.style.paddingRight : this.centerPadding,
                    paddingBottom: this.style.paddingBottom,
                    paddingLeft: this.style.paddingLeft,
                    borderWidth: this.style.borderWidth,
                    borderStyle: 'solid',
                    borderColor: this.style.borderColor
                };
            },

            rightColumnPaddings() {
                return {
                    backgroundColor: this.style.backgroundColor,
                    paddingTop: this.style.paddingTop,
                    paddingRight: this.style.paddingRight,
                    paddingBottom: this.style.paddingBottom,
                    paddingLeft: this.hasBorder ? this.style.paddingLeft : this.centerPadding,
                    borderWidth: this.style.borderWidth,
                    borderStyle: 'solid',
                    borderColor: this.style.borderColor
                };
            },

            wrapperStyle() {
                const minWidth = this.HALF_WIDTH - parseInt(this.style.borderWidth, 10);

                return {
                    display: 'inline-block',
                    maxWidth: this.twoColumns ? '50%' : '100%',
                    minWidth: `${minWidth}px`,
                    verticalAlign: 'top',
                    width: '100%'
                };
            },

            wrapperTdStyle() {
                const padding = (parseInt(this.style.borderWidth, 10) && this.twoColumns) ? this.centerPadding : 0;

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

            imageStyle() {
                const padding = parseInt(this.style.paddingLeft, 10);
                const border = parseInt(this.style.borderWidth, 10);

                let width = 0;

                if (this.twoColumns) {
                    if (border) {
                        width = this.HALF_WIDTH - (padding / this.TWO_COLUMNS) - (this.TWO_COLUMNS * border) - (this.TWO_COLUMNS * padding);
                    } else {
                        width = this.HALF_WIDTH - (padding + (padding / this.TWO_COLUMNS));
                    }
                } else {
                    width = this.FULL_WIDTH - (this.TWO_COLUMNS * padding) - border;
                }

                width = Math.floor(width);

                return {
                    maxWidth: `${width}px`,
                    height: 'auto'
                };
            }
        }
    };
</script>
