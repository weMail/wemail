<template>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" :style="containerOuterStyle">
        <tr v-if="content.images && content.images.length">
            <td align="center" valign="top" :style="containerInnerStyle">
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
                                                        <div class="wemail-content-image">
                                                            <a v-if="content.images[0].link" :href="content.images[0].link" target="_blank">
                                                                <img :src="content.images[0].src" :style="leftImageStyle" :alt="content.images[0].alt">
                                                            </a>

                                                            <img v-else :src="content.images[0].src" :style="leftImageStyle" :alt="content.images[0].alt">
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
                <div v-if="twoColumns" :style="rightWrapperStyle" class="wrapper">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px;" class="wrapper">
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="rightColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-image">
                                                            <a v-if="content.images[1].link" :href="content.images[1].link" target="_blank">
                                                                <img :src="content.images[1].src" :style="rightImageStyle" :alt="content.images[1].alt">
                                                            </a>

                                                            <img v-else :src="content.images[1].src" :style="rightImageStyle" :alt="content.images[1].alt">
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
            <td align="center" valign="top" :style="containerInnerStyle">
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

            containerOuterStyle() {
                return {
                    maxWidth: '600px',
                    borderWidth: this.style.borderWidth,
                    borderStyle: 'solid',
                    borderColor: this.style.borderColor
                };
            },

            containerInnerStyle() {
                return {
                    backgroundColor: this.style.backgroundColor
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
                    paddingTop: this.style.paddingTop,
                    paddingRight: this.centerPadding,
                    paddingBottom: this.style.paddingBottom,
                    paddingLeft: this.style.paddingLeft
                };
            },

            rightColumnPaddings() {
                return {
                    paddingTop: this.style.paddingTop,
                    paddingRight: this.style.paddingRight,
                    paddingBottom: this.style.paddingBottom,
                    paddingLeft: this.centerPadding
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

            rightWrapperStyle() {
                return $.extend(true, {
                    float: 'right'
                }, this.wrapperStyle);
            },

            leftImageStyle() {
                const leftPadding = parseInt(this.leftColumnPaddings.paddingLeft, 10);
                const rightPadding = parseInt(this.leftColumnPaddings.paddingRight, 10);

                let width = 0;
                const borderWidth = parseInt(this.style.borderWidth, 10);

                if (this.twoColumns) {
                    width = this.HALF_WIDTH - (leftPadding + rightPadding + borderWidth);
                } else {
                    width = this.FULL_WIDTH - (leftPadding + rightPadding + borderWidth);
                }

                return {
                    maxWidth: `${width}px`,
                    height: 'auto'
                };
            },

            rightImageStyle() {
                const leftPadding = parseInt(this.rightColumnPaddings.paddingLeft, 10);
                const rightPadding = parseInt(this.rightColumnPaddings.paddingRight, 10);

                let width = 0;
                const borderWidth = parseInt(this.style.borderWidth, 10);

                if (this.twoColumns) {
                    width = this.HALF_WIDTH - (leftPadding + rightPadding + borderWidth);
                } else {
                    width = this.FULL_WIDTH - (leftPadding + rightPadding + borderWidth);
                }

                return {
                    maxWidth: `${width}px`,
                    height: 'auto'
                };
            }
        }
    };
</script>
