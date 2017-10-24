<template>
    <div>
        <h1>{{ i18n.campaignSummery }}</h1>

        <table class="form-table">
            <tbody>
                <tr>
                    <th>{{ i18n.emailSubject }}</th>
                    <td class="edit-send-email-subject">
                        <div class="row">
                            <div class="col-6">
                                <div class="validate-field">
                                    <p class="top-hint clearfix">
                                        <span class="invalid-msg">{{ i18n.thisFieldIsRequired }}</span>
                                        <span :class="['float-right', subRemainingCharClass]">
                                            {{ subRemainingChar }} {{ i18n.charactersRemaining }}
                                        </span>
                                    </p>

                                    <input
                                        type="text"
                                        class="block campaign-subject-input"
                                        v-model="email.subject"
                                        :maxlength="subMaxLength"
                                        data-validator="isEmptyInput"
                                        @focus="focusFloatingInfoInput"
                                        @blur="blurFloatingInfoInput"
                                    >

                                    <div class="wemail-dropdown" id="shortcode-wemail-dropdown">
                                        <button type="button" class="button" data-toggle="wemail-dropdown">
                                           <img :src="customizer.shortcodeImg" alt="">
                                        </button>
                                        <div class="wemail-dropdown-menu wemail-dropdown-menu-right">
                                            <template v-for="(shortcodeObj, shortcodeType) in customizer.shortcodes">
                                                <a href="#" class="wemail-dropdown-item shortcode-type">
                                                    {{ shortcodeObj.title }}
                                                </a>

                                                <a
                                                    v-for="(codeObj, shortcode) in shortcodeObj.codes"
                                                    class="wemail-dropdown-item"
                                                    href="#"
                                                    @click.prevent="addShortcode(shortcodeType, shortcode, codeObj)"
                                                >{{ codeObj.title }}</a>

                                                <div class="wemail-dropdown-divider"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.preHeader }}</th>
                    <td>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" class="block" v-model="email.pre_header">
                                <p class="hint">{{ i18n.preHeaderHint }}</p>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.sender }}</th>
                    <td>
                        <div class="row">
                            <div class="col-3">
                                <div class="validate-field">
                                    <p class="top-hint clearfix">
                                        <span class="field-placeholder">{{ i18n.name }}</span>
                                        <span class="invalid-msg">{{ i18n.thisFieldIsRequired }}</span>
                                    </p>

                                    <input
                                        type="text"
                                        class="block"
                                        v-model="email.sender_name"
                                        data-validator="isEmptyInput"
                                        @focus="focusFloatingInfoInput"
                                        @blur="blurFloatingInfoInput"
                                    >
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="validate-field">
                                    <p class="top-hint clearfix">
                                        <span class="field-placeholder">{{ i18n.email }}</span>
                                        <span class="invalid-msg">{{ i18n.invalidEmailAddress }}</span>
                                    </p>

                                    <input
                                        type="text"
                                        class="block"
                                        v-model="email.sender_email"
                                        data-validator="isInvalidEmail"
                                        @focus="focusFloatingInfoInput"
                                        @blur="blurFloatingInfoInput"
                                    >
                                </div>
                            </div>
                        </div>
                        <p class="hint">{{ i18n.senderHint }}</p>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.replyTo }}</th>
                    <td>
                        <div class="row">
                            <div class="col-3">
                                <div class="validate-field">
                                    <p class="top-hint clearfix">
                                        <span class="field-placeholder">{{ i18n.name }}</span>
                                        <span class="invalid-msg">{{ i18n.thisFieldIsRequired }}</span>
                                    </p>

                                    <input
                                        type="text"
                                        class="block"
                                        v-model="email.reply_to_name"
                                        data-validator="isEmptyInput"
                                        @focus="focusFloatingInfoInput"
                                        @blur="blurFloatingInfoInput"
                                    >
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="validate-field">
                                    <p class="top-hint clearfix">
                                        <span class="field-placeholder">{{ i18n.email }}</span>
                                        <span class="invalid-msg">{{ i18n.invalidEmailAddress }}</span>
                                    </p>

                                    <input
                                        type="text"
                                        class="block"
                                        v-model="email.reply_to_email"
                                        data-validator="isInvalidEmail"
                                        @focus="focusFloatingInfoInput"
                                        @blur="blurFloatingInfoInput"
                                    >
                                </div>
                            </div>
                        </div>
                        <p class="hint">{{ i18n.reply_toHint }}</p>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.subscribers }}</th>
                    <td>
                    <div class="row">
                        <div class="col-6">
                            <p class="clearfix">
                                <strong>{{ i18n.lists }}</strong>
                            </p>
                            <ul class="list-disc" v-if="lists.length">
                                <li v-for="list in lists">{{ list.name }}</li>
                            </ul>
                            <ul v-else >
                                <li><em class="text-muted">{{ i18n.noListSelected }}</em></li>
                            </ul>

                            <hr>

                            <p class="clearfix">
                                <strong>{{ i18n.segments }}</strong>
                            </p>
                            <ul class="list-disc" v-if="segments.length">
                                <li v-for="segment in segments">{{ segment.name }}</li>
                            </ul>
                            <ul v-else>
                                <li><em class="text-muted">{{ i18n.noSegmentSelected }}</em></li>
                            </ul>
                        </div>
                    </div>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.scheduleCampaign }}</th>
                    <td>
                        <label v-if="!isScheduled">
                            <input type="checkbox" v-model="isScheduled"> {{ i18n.yesScheduleIt }}
                        </label>

                        <div v-else>
                            <input type="checkbox" v-model="isScheduled">&nbsp;
                            <datepicker v-model="deliverDate" :placeholder="serverDate"></datepicker>&nbsp;&nbsp;@&nbsp;
                            <timepicker v-model="deliverTime" :placeholder="serverTime"></timepicker>
                            <br>

                            <p class="hint">{{ i18n.currentServerTimeIs }} {{ serverDate }} {{ serverTime }}</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>{{ i18n.googleAnalyticCampaign }}</th>
                    <td>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" class="block" v-model="campaign.utm_campaign">
                                <p class="hint">{{ i18n.utmHint }}</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <pre>{{ campaign }}</pre> -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                subMaxLength: 150,
                isScheduled: false
            };
        },

        computed: {
            ...Vuex.mapState('campaignEdit', ['i18n', 'campaign', 'customizer']),

            email() {
                return this.campaign.email;
            },

            subRemainingChar() {
                let remainingChar = this.subMaxLength;

                if (this.email.subject) {
                    remainingChar -= this.email.subject.length;
                }

                return (remainingChar < 0) ? 0 : remainingChar;
            },

            subRemainingCharClass() {
                let className = '';
                const WARN_LIMIT = 30;

                if (this.subRemainingChar > 0 && this.subRemainingChar <= WARN_LIMIT) {
                    className = 'text-warning';

                } else if (this.subRemainingChar <= 0) {
                    className = 'text-danger';
                }

                return className;
            },

            deliverDate: {
                get() {
                    if (!this.campaign.deliver_at) {
                        return moment.tz(weMail.dateTime.server.timezone).add(1, 'd').format('YYYY-MM-DD');
                    }

                    return this.campaign.deliver_at.split(' ')[0];
                },

                set(date) {
                    this.campaign.deliver_at = `${date} ${this.deliverTime}`;
                }
            },

            deliverTime: {
                get() {
                    if (!this.campaign.deliver_at) {
                        return moment.tz(weMail.dateTime.server.timezone).add(1, 'd').format('HH:mm:ss');
                    }

                    return this.campaign.deliver_at.split(' ')[1];
                },

                set(time) {
                    this.campaign.deliver_at = `${this.deliverDate} ${time}`;
                }
            },

            lists() {
                const vm = this;

                return this.$store.state.campaignEdit.lists.filter((list) => {
                    return vm.campaign.lists.indexOf(list.id) >= 0;
                });
            },

            segments() {
                const vm = this;

                return this.$store.state.campaignEdit.segments.filter((segment) => {
                    return vm.campaign.segments.indexOf(segment.id) >= 0;
                });
            },

            serverDateTime() {
                return moment.tz(weMail.dateTime.server.timezone);
            },

            serverDate() {
                return weMail.dateTime.toMoment(weMail.dateTime.server.dateFormat, this.serverDateTime);
            },

            serverTime() {
                return weMail.dateTime.toMoment(weMail.dateTime.server.timeFormat, this.serverDateTime);
            }
        },

        methods: {
            focusFloatingInfoInput(e) {
                $(e.target).parent().addClass('has-length');
                $(e.target).parent().removeClass('invalid-field');
            },

            blurFloatingInfoInput(e) {
                const vm = this;
                const input = $(e.target);
                const inputVal = input.val();

                if (!inputVal.length) {
                    input.parent().removeClass('has-length');
                }

                if (e.target.dataset.validator && typeof this[e.target.dataset.validator] === 'function') {
                    if (this[e.target.dataset.validator].call(vm, inputVal)) {
                        $(e.target).parent().addClass('invalid-field');
                    } else {
                        $(e.target).parent().removeClass('invalid-field');
                    }
                }
            },

            isEmptyInput(inputVal) {
                return !inputVal;
            },

            isInvalidEmail(email) {
                return !(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email));
            },

            addShortcode(shortcodeType, shortcode, codeObj) {
                let code = `[${shortcodeType}:${shortcode}]`;

                if (codeObj.default) {
                    code = `[${shortcodeType}:${shortcode} default="${codeObj.default}"]`;
                }

                if (codeObj.text) {
                    code = `[${shortcodeType}:${shortcode} text="${codeObj.text}"]`;
                }

                if (codeObj.plainText) {
                    code = codeObj.text;
                }

                this.email.subject = this.email.subject ? `${this.email.subject} ${code}` : code;

                $(this.$el).find('.campaign-subject-input').focus();

                // should be move cursor to end also
            }
        }
    };
</script>

<style lang="scss">
    .campaign-subject-input {
        padding-right: 35px !important;
    }

    #shortcode-wemail-dropdown {
        position: absolute;
        top: 1px;
        right: 11px;

        button {
            width: 27px;
            height: 27px;
            padding: 3px;
            border-width: 0 0 0 1px;
            border-radius: 0;
            box-shadow: none;

            img {
                width: 100%;
                height: auto;
            }
        }

        .wemail-dropdown-menu {
            top: -2px !important;
            left: 1px !important;
            max-height: 220px;
            overflow-y: auto;

            .shortcode-type {
                font-size: 14px;
                font-weight: 700;
            }
        }
    }
</style>
