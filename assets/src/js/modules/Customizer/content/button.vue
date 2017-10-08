<template>
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" :style="outerStyle">
        <tr>
            <td align="center" valign="top" style="font-size: 0;">
                <div class="wrapper" :style="wrapperStyle" >
                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="600px">
                        <tr>
                            <td align="center" valign="top">
                                <table class="wrapperInner" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td :style="innerStyle">
                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                <tr>
                                                    <td valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-button">
                                                            <a :href="href" :title="content.title" :style="style">{{ content.text }}</a>
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
                required: false
            }
        },

        computed: {
            style() {
                return this.content.style;
            },

            containerStyle() {
                return this.content.containerStyle;
            },

            outerStyle() {
                return {
                    maxWidth: '600px',
                    marginBottom: this.containerStyle.marginBottom
                };
            },

            wrapperStyle() {
                return {
                    display: 'inline-block',
                    maxWidth: '100%',
                    minWidth: '600px',
                    verticalAlign: 'top',
                    width: '100%'
                };
            },

            innerStyle() {
                return {
                    textAlign: this.containerStyle.textAlign,
                    backgroundColor: this.containerStyle.backgroundColor,
                    padding: this.containerStyle.padding
                };
            },

            href() {
                return this.content.href ? this.content.href : weMail.siteURL;
            }
        }
    };
</script>
