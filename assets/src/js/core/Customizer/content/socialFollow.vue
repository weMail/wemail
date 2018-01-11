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
                                                    <td :align="containerTdStyle" valign="middle" :style="{fontSize: globalCss.fontSize}">
                                                        <div class="wemail-content-social-follow" :style="containerStyle">
                                                            <table v-for="(icon, index) in content.icons" :class="iconClasses" :style="containerTableStyles[index]" border="0" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="middle" v-if="content.display === 'both' || content.display === 'icon'" class="image-container-td">
                                                                        <a :href="icon.link ? icon.link : '#'"><img :src="imageUrls[icon.site]" :alt="icon.text"></a>
                                                                    </td>
                                                                    <td valign="middle" v-if="content.display === 'both' || content.display === 'text'">
                                                                        <a :href="icon.link ? icon.link : '#'" :style="textCss">{{ icon.text }}</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
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

            outerStyle() {
                return {
                    maxWidth: '600px',
                    marginBottom: this.style.marginBottom
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

            iconClasses() {
                const classNames = ['social-follow-table'];

                if (this.content.layout === 'vertical') {
                    classNames.push('vertical-icons');
                } else {
                    classNames.push('horizontal-icons');
                }

                if (this.content.size === 'large') {
                    classNames.push('large-icons');
                } else {
                    classNames.push('default-icons');
                }

                classNames.push(`align-${this.style.textAlign}`);

                return classNames;
            },

            containerTableStyles() {
                const styles = [];
                const totalIcons = this.content.icons.length;

                let margin = {};
                let i = 0;

                if (this.content.layout === 'vertical') {
                    margin = {
                        marginBottom: this.content.iconMargin
                    };
                } else {
                    margin = {
                        marginRight: this.content.iconMargin
                    };
                }

                for (i = 0; i < totalIcons; i++) {
                    if (i < (totalIcons - 1)) {
                        styles.push(margin);
                    } else {
                        styles.push({});
                    }
                }

                return styles;
            },

            containerStyle() {
                let isInline = false;

                if (this.content.layout === 'vertical' && this.content.size === 'default' && this.style.textAlign === 'right') {
                    isInline = true;
                }

                if (this.content.layout === 'vertical' && this.content.size === 'large' && this.style.textAlign === 'left') {
                    isInline = true;
                }

                if (this.content.layout === 'vertical' && this.content.size === 'large' && this.style.textAlign === 'right') {
                    isInline = true;
                }

                return {
                    lineHeight: 0,
                    textAlign: this.style.textAlign,
                    padding: this.style.padding,
                    display: isInline ? 'inline-block' : 'block'
                };
            },

            containerTdStyle() {
                let align = 'left';

                if (this.content.layout === 'vertical' && this.content.size === 'default' && this.style.textAlign === 'right') {
                    align = 'right';
                }

                if (this.content.layout === 'vertical' && this.content.size === 'large' && this.style.textAlign === 'right') {
                    align = 'right';
                }

                return align;
            },

            innerStyle() {
                return {
                    backgroundColor: this.style.backgroundColor,
                    padding: this.style.padding,
                    borderWidth: this.style.borderWidth,
                    borderStyle: 'solid',
                    borderColor: this.style.borderColor
                };
            },

            imageUrls() {
                const vm = this;
                const urls = {};

                this.content.icons.forEach((icon) => {
                    urls[icon.site] = `${vm.customizer.cdn}/social-icons/${vm.content.iconStyle}-${icon.site}.png`;
                });

                return urls;
            },

            textCss() {
                return {
                    color: this.style.color,
                    textTransform: this.style.textTransform,
                    fontSize: this.style.fontSize,
                    fontWeight: this.style.fontWeight
                };
            }
        }
    };
</script>
