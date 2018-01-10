<template>
    <div>
        <h1 class="margin-bottom-10">{{ __('Import') }}</h1>

        <div class="row">
            <div v-for="integration in integrations" class="col-md-4">
                <div class="postbox postbox-import">
                    <h3 class="title">{{ integration.title }}</h3>

                    <router-link :to="{name: integration.route}">
                        <img :src="integration.logo" alt="">
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
                integrations: [
                    {
                        name: 'mailchimp',
                        title: __('MailChimp'),
                        logo: `${weMail.cdn}/vendor/mailchimp.png`,
                        route: 'importMailChimpSettings'
                    },
                    {
                        name: 'mailpoet',
                        title: __('MailPoet 2'),
                        logo: `${weMail.cdn}/vendor/mailpoet-2.png`,
                        route: 'importMailPoetList'
                    }
                ]
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
