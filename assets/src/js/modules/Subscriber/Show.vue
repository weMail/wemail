<template>
    <div v-if="isLoaded" class="wemail-single-subscriber">
        <div class="row">
            <div class="col-md-5">
                <div class="wemail-box">
                    <div class="row">
                        <div class="col-4 line-height-0">
                            <img class="profile-image" :src="dummyImageURL" alt="">
                        </div>
                        <div class="col-8 no-left-padding">
                            <div class="profile-summery">
                                <h1 class="subscriber-name">{{ subscriberName }}</h1>

                                <a class="subscriber-email" :href="`mailto:${subscriber.email}`">{{ subscriber.email }}</a>

                                <ul v-if="networks.length" class="subscriber-networks list-inline">
                                    <li v-for="network in networks" class="list-inline-item">
                                        <a :href="network.link" v-html="network.icon"></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                activity list
            </div>
        </div>
        <pre>{{ dummyImageURL }}</pre>
        <pre>{{ socialNetworks }}</pre>
        <pre>{{ subscriber }}</pre>
    </div>
</template>

<script>
    export default {
        routeName: 'subscriberShow',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('subscriberShow', ['i18n', 'subscriber', 'dummyImageURL', 'socialNetworks']),

            meta() {
                return this.subscriber.meta;
            },

            subscriberName() {
                if (!this.subscriber.first_name) {
                    return this.i18n.noName;
                }

                const name = [this.subscriber.first_name, this.subscriber.last_name];

                return name.join(' ');
            },

            networks() {
                const vm = this;
                const networks = [];

                this.socialNetworks.networks.forEach((network) => {
                    if (vm.meta[network]) {
                        networks.push({
                            name: network,
                            title: vm.i18n[network],
                            link: vm.meta[network],
                            icon: vm.socialNetworks.icons[network]
                        });
                    }
                });

                return networks;
            }
        }
    };
</script>

<style lang="scss">
    .wemail-single-subscriber {

        .profile-image {
            max-width: 100%;
            border: 1px solid $wp-border-color-darken;
        }

        .profile-summery {

            .subscriber-name {
                padding: 0;
                margin-bottom: 5px;
                line-height: 1.2;
            }

            .subscriber-email {
                text-decoration: none;
            }

            .subscriber-networks {

                a {
                    font-size: 18px;
                    color: $wp-black;
                }
            }
        }
    }
</style>
