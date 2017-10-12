<template>
    <div class="editor-progress-bar clearfix">
        <button
            v-if="scope === 'edit'"
            type="button"
            class="button float-left"
            @click="gotoPrevStep"
            :disabled="isPrevBtnDisabled"
        ><i class="fa fa-angle-double-left"></i> {{ i18n.previous }}</button>

        <div class="steps">
            <ul>
                <li v-for="step in steps" :class="[(currentStep === step) ? 'active' : '']">
                    <a v-if="scope === 'create'" href="#" @click.prevent :class="getStepLinkClass(step)">
                        <span>{{ i18n[step] }}</span>
                    </a>
                    <router-link
                        v-else
                        :to="getStepLink(step)"
                    ><span>{{ i18n[step] }}</span></router-link>
                </li>
            </ul>
        </div>

        <button
            v-if="scope === 'edit'"
            type="button"
            class="button float-right"
            @click="gotoNextStep"
            :disabled="isNextBtnDisabled"
        >{{ i18n.next }} <i class="fa fa-angle-double-right"></i></button>
    </div>
</template>

<script>
    export default {
        props: {
            scope: {
                type: String,
                default: 'edit'
            },

            i18n: {
                type: Object,
                default: {}
            }
        },

        data() {
            return {
                steps: ['setup', 'template', 'design', 'send']
            };
        },

        computed: {
            currentStep() {
                return weMail._.last(this.$route.path.split('/'));
            },

            showNextBtn() {
                return (this.scope === 'edit') || false;
            },

            isNextBtnDisabled() {
                let isDisabled = false;

                if (this.currentStep === 'send') {
                    isDisabled = true;
                }

                return isDisabled;
            },

            isPrevBtnDisabled() {
                let isDisabled = false;

                if (this.currentStep === 'setup') {
                    isDisabled = true;
                }

                return isDisabled;
            }
        },

        methods: {
            getStepLink(step) {
                const name = `campaignEdit${weMail._.upperFirst(step)}`;

                return {
                    name,
                    params: {
                        id: this.$route.params.id
                    }
                };
            },

            getStepLinkClass(step) {
                return (this.scope === 'create' && step !== 'setup') ? ['disabled'] : [];
            },

            gotoNextStep() {
                const nextStep = this.steps[this.steps.indexOf(this.currentStep) + 1];

                this.$router.push({
                    name: `campaignEdit${weMail._.upperFirst(nextStep)}`,
                    params: {
                        id: this.$route.params.id
                    }
                });
            },

            gotoPrevStep() {
                const prevStep = this.steps[this.steps.indexOf(this.currentStep) - 1];

                this.$router.push({
                    name: `campaignEdit${weMail._.upperFirst(prevStep)}`,
                    params: {
                        id: this.$route.params.id
                    }
                });
            }
        }
    };
</script>

<style lang="scss">
    .editor-progress-bar {
        position: fixed;
        bottom: 0;
        left: 160px;
        z-index: 9991;
        width: calc(100% - 160px);
        height: 46px;
        text-align: center;
        background-color: $wp-border-color;
        border-top: 1px solid $wp-border-color-darken;
        box-shadow: 0 4px 9px 1px #000;

        & > .steps {
            position: relative;
            display: inline-block;
            width: auto;
            padding: 0;
            margin: 7px auto;
            overflow: hidden;
            color: #444;
            background-color: #2a5f9e;
            border-radius: 3px;

            ul {
                position: relative;
                width: auto;
                padding: 0;
                margin: 0 0 0 -25px;

                li {
                    position: relative;
                    box-sizing: border-box;
                    display: inline;

                    &:first-child {

                        span:before {
                            display: none;
                        }
                    }

                    &.active {

                        a {
                            font-weight: 500;
                            color: $wp-blue;
                            cursor: default;
                        }
                    }

                    a {
                        font-weight: 500;
                        color: #444;
                        text-decoration: none;

                        &.disabled {
                            color: rgba(68, 68, 68, 0.47);
                            pointer-events: none;
                            cursor: default;
                        }

                        span {
                            position: relative;
                            display: block;
                            float: left;
                            padding: 7px 30px 7px 38px;
                            text-decoration: none;
                            background-color: $wp-border-color;

                            &:before {
                                position: absolute;
                                top: -4px;
                                left: 0;
                                content: "";
                                border-top: 20px solid rgba(0, 0, 0, 0);
                                border-bottom: 20px solid rgba(0, 0, 0, 0);
                                border-left: 7px solid $wp-border-color-darken;
                            }

                            &:after {
                                position: absolute;
                                top: -4px;
                                right: -5px;
                                z-index: 1;
                                content: "";
                                border-top: 20px solid rgba(0, 0, 0, 0);
                                border-bottom: 20px solid rgba(0, 0, 0, 0);
                                border-left: 6px solid $wp-border-color;
                            }
                        }
                    }
                }
            }
        }

        .button {
            height: 46px;
            padding: 0 35px;
            text-shadow: 1px 1px 0 #cacaca;
            background-color: $wp-border-color;
            border: 0;
            border-radius: 0;
            box-shadow: none;

            @include transition;

            &:active {
                box-shadow: none;
            }

            &:hover {
                color: $wp-blue;
            }

            &.float-left {
                border-right: 1px solid $wp-border-color-darken;
            }

            &.float-right {
                border-left: 1px solid $wp-border-color-darken;
            }
        }
    }
</style>
