<template>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" :style="containerOuterStyle">
        <tr>
            <td align="center" valign="top" :style="containerInnerStyle">
                <div :style="wrapperStyle" class="wrapper">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" :style="{maxWidth: content.twoColumns ? '300px' : '600px'}" class="wrapper">
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="leftColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-text" v-html="content.texts[0]"></div>
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
                <div v-if="content.twoColumns" :style="wrapperStyle" class="wrapper">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px; float: right" class="wrapper">
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="rightColumnPaddings">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td align="left" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-text" v-html="content.texts[1]"></div>
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
            }
        },

        computed: {
            style() {
                return this.content.style;
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
                    fontSize: 0,
                    backgroundColor: this.style.backgroundColor,
                    color: this.style.color
                };
            },

            centerPadding() {
                if (!this.content.twoColumns) {
                    return this.style.paddingRight;
                }

                const TWO_COLUMNS = 2;
                const padding = Math.ceil(parseInt(this.style.paddingRight, 10) / TWO_COLUMNS);

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
                const HALF_WIDTH = 300;
                const minWidth = HALF_WIDTH - parseInt(this.style.borderWidth, 10);

                return {
                    display: 'inline-block',
                    maxWidth: this.content.twoColumns ? '50%' : '100%',
                    minWidth: `${minWidth}px`,
                    verticalAlign: 'top',
                    width: '100%'
                };
            }
        }
    };
</script>
