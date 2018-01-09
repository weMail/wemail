<template>
    <div>
        <h1 class="margin-bottom-10">{{ __('Import') }}</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="postbox postbox-import">
                    <h3 class="title">{{ __('MailChimp') }}</h3>

                    <router-link :to="{name: 'importMailChimpSettings'}">
                        <img :src="logo.mailchimp" alt="">
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                logo: {
                    mailchimp: `${weMail.cdn}/third-party/mailchimp-logo.png`
                }
            };
        }
    };
</script>

<style lang="scss">
    .postbox-import {

        a {
            display: block;
            padding: 12px;
            overflow: hidden;
            text-align: center;

            img {
                width: auto;
                height: auto;
            }
        }
    }
</style>
