<template>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" :style="containerOuterStyle">
        <tr>
            <td align="center" valign="top" :style="containerInnerStyle">
                <div class="wrapper" :style="wrapperStyle.left" >
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" :style="{maxWidth: content.twoColumns ? '300px' : '600px'}">
                        <tr>
                            <td align="center" valign="top" :style="wrapperTdStyle[0]">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="leftColumnStyle">
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
                <div v-if="content.twoColumns" class="wrapper" :style="wrapperStyle.right">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px; float: right">
                        <tr>
                            <td align="center" valign="top" :style="wrapperTdStyle[1]">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="rightColumnStyle">
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
            },

            customizer: {
                type: Object,
                required: false
            }
        },

        data() {
            return {
                two: 2,
                fullWidth: 600,
                halfWidth: 300
            };
        },

        computed: {
            style() {
                return this.content.style;
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
                if (!this.content.twoColumns) {
                    return this.style.paddingRight;
                }

                const TWO_COLUMNS = 2;
                const padding = Math.floor(parseInt(this.style.paddingRight, 10) / TWO_COLUMNS);

                return `${padding}px`;
            },

            leftColumnStyle() {
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

            rightColumnStyle() {
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
                let leftMaxWidth = 0;
                let leftMinWidth = 0;
                let rightMaxWidth = 0;
                let rightMinWidth = 0;

                if (!this.content.twoColumns) {
                    leftMaxWidth = '100%';
                    leftMinWidth = '600';

                } else if (this.content.columnSplit === '1-1') {
                    const minWidth = this.halfWidth - (this.two * parseInt(this.style.borderWidth, 10));

                    leftMaxWidth = '50%';
                    leftMinWidth = minWidth;

                    rightMaxWidth = '50%';
                    rightMinWidth = minWidth;

                } else if (this.content.columnSplit === '2-1') {
                    leftMaxWidth = '66.6666666666%';
                    leftMinWidth = '400';

                    rightMaxWidth = '33.3333333333%';
                    rightMinWidth = '200';

                } else if (this.content.columnSplit === '1-2') {
                    leftMaxWidth = '33.3333333333%';
                    leftMinWidth = '200';

                    rightMaxWidth = '66.6666666666%';
                    rightMinWidth = '400';
                }

                return {
                    left: {
                        display: 'inline-block',
                        maxWidth: leftMaxWidth,
                        minWidth: `${leftMinWidth}px`,
                        verticalAlign: 'top',
                        width: '100%'
                    },

                    right: {
                        display: 'inline-block',
                        maxWidth: rightMaxWidth,
                        minWidth: `${rightMinWidth}px`,
                        verticalAlign: 'top',
                        width: '100%'
                    }
                };
            },

            wrapperTdStyle() {
                const padding = (this.hasBorder && this.content.twoColumns) ? this.centerPadding : 0;

                return [
                    {
                        paddingRight: padding
                    },
                    {
                        paddingLeft: padding
                    }
                ];
            }
        }
    };
</script>
