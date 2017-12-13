<template>
    <div v-if="isLoaded" class="row settings-smtp">
        <div class="col-12 margin-bottom-40">
            <ul class="list-sub">
                <template v-for="driver in drivers">
                    <li :class="[showDriver === driver.name ? 'active' : '']">
                        <a href="#" @click.prevent="showDriver = driver.name">
                            {{ driver.title }}
                        </a>
                    </li>
                </template>
            </ul>
        </div>

        <div v-if="showDriver === 'smtp'" class="col-sm-6">
            <label>
                <input type="checkbox" class="margin-right-5" v-model="activeDriver">
                <strong class="d-inline">{{ __('Enable SMTP') }}</strong>
            </label>

            <label>
                <strong>{{ __('Mail Server') }}</strong>
                <input type="text" v-model="settings.smtp.host" :placeholder="__('smtp.gmail.com')">
                <em class="hint">{{ __('SMTP host address') }}</em>
            </label>

            <label>
                <strong>{{ __('Port') }}</strong>
                <input type="text" v-model="settings.smtp.port" :placeholder="__('587')">
                <em class="hint">{{ __('SSL: 465 | TLS: 587') }}</em>
            </label>

            <label>
                <strong>{{ __('Encryption') }}</strong>
                <select v-model="settings.smtp.encryption">
                    <option value="">{{ __('None') }}</option>
                    <option value="ssl">{{ __('SSL') }}</option>
                    <option value="tls">{{ __('TLS') }}</option>
                </select>
                <em class="hint">{{ __('Encryption type') }}</em>
            </label>

            <label>
                <strong>{{ __('Username') }}</strong>
                <input type="text" v-model="settings.smtp.username">
                <em class="hint">{{ __('Your SMTP username') }}</em>
            </label>

            <label>
                <strong>{{ __('Password') }}</strong>
                <input type="password" v-model="settings.smtp.password">
                <em class="hint">{{ __('Your SMTP password') }}</em>
            </label>
        </div>

        <div v-if="showDriver === 'sparkpost'" class="col-sm-6">
            <label>
                <strong>{{ __('Enable Sparkpost') }}</strong>
                <input type="checkbox" v-model="activeDriver">
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'settingsEmailOutbound',

        mixins: weMail.getMixins('settings'),

        data() {
            return {
                showDriver: '',
                drivers: [
                    {
                        name: 'smtp',
                        title: __('SMTP')
                    },
                    {
                        name: 'sparkpost',
                        title: __('Sparkpost')
                    }
                ]
            };
        },

        computed: {
            activeDriver: {
                get() {
                    return (this.settings.driver === this.showDriver) || false;
                },

                set(active) {
                    if (active) {
                        this.settings.driver = this.showDriver;
                    } else {
                        this.settings.driver = false;
                    }
                }
            }
        },

        methods: {
            afterLoaded() {
                this.showDriver = this.settings.driver || 'smtp';
            },

            showDriverSettings(driver) {
                console.log(driver);
            }
        }
    };
</script>

<style lang="scss">
</style>
