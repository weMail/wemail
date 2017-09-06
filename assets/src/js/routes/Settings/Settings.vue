<template>
    <div>
        <h1>{{ i18n.settings }}</h1>

        <div class="wemail-settings d-flex">
            <div class="wemail-settings-sidebar">
                <ul>
                    <template v-for="route in childRoutes">
                        <router-link :to="route" active-class="active" tag="li">
                            <a>{{ route.menu }}</a>
                        </router-link>
                    </template>
                </ul>
            </div>
            <div class="wemail-settings-content">
                <h4 class="settings-title">{{ settingsTitle }}</h4>

                <form class="settings-form" @submit.prevent="saveSettings" disabled>
                    <router-view></router-view>
                    <button type="submit" class="button button-primary submit-button">{{ i18n.saveSettings }}</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'settings',

        mixins: [weMail.mixins.routeComponent],

        data() {
            return {
                settingsTitle: ''
            };
        },

        computed: {
            ...weMail.Vuex.mapState('settings', ['i18n']),

            childRoutes() {
                const children = weMail._.filter(weMail.routes, {
                    name: 'settings'
                })[0].children;

                return children;
            }
        },

        created() {
            this.setSettingsTitle();
        },

        watch: {
            $route() {
                this.setSettingsTitle();
            }
        },

        methods: {
            setSettingsTitle() {
                this.settingsTitle = weMail._.chain(this.childRoutes).filter({
                    name: this.$router.currentRoute.name
                }).head().value().menu;
            },

            saveSettings() {
                const vm = this;
                const currentRoute = vm.$route.name;

                vm.$root.showLoadingAnime = true;

                weMail.ajax.post('save_settings', {
                    name: vm.$route.name,
                    settings: vm.$store.state[currentRoute].settings
                }).done((response) => {
                    console.log(response);
                }).always(() => {
                    vm.$root.showLoadingAnime = false;
                });
            }
        }
    };
</script>

<style lang="scss">
    .wemail-settings {
        margin: 16px 0;
    }

    .wemail-settings-sidebar {
        position: relative;
        z-index: 1;
        min-width: 185px;
        background-color: #eaeaea;
        border-bottom: 1px solid $wp-border-color-darken;
        border-left: 1px solid $wp-border-color-darken;

        & > ul {
            margin: 0;

            & > li {
                margin: 0;

                a {
                    display: block;
                    padding: 0 20px;
                    margin: 0 -1px 0 0;
                    overflow: hidden;
                    font-size: 13px;
                    font-weight: 700;
                    line-height: 3;
                    color: #777;
                    text-decoration: none;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    border-top: 1px solid #f7f5f5;
                    border-bottom: 1px solid $wp-border-color-darken;

                    &:active,
                    &:focus {
                        outline: 0;
                        box-shadow: none;
                    }
                }

                &.active a {
                    color: $wp-black;
                    background-color: #fff;
                    border-right-color: #fff;
                }

                &:first-child a {
                    border-top-color: $wp-border-color-darken;
                }

                &:last-child a {
                    margin-bottom: 60px;
                }
            }
        }
    }

    .wemail-settings-content {
        position: relative;
        width: 100%;
        padding: 10px 20px;
        background-color: #fff;
        border: 1px solid $wp-border-color-darken;

        .settings-title {
            padding-bottom: 16px;
            margin: 8px 0 16px;
            font-size: 18px;
            font-weight: 300;
            border-bottom: 1px solid $wp-border-color-darken;
        }

        .settings-form {

            .submit-button {
                margin: 10px 0;
            }

            label,
            .multiselect-container {
                display: block;
                margin-bottom: 18px;

                & > strong {
                    display: block;
                    margin-bottom: 4px;
                }
            }

            input[type="text"],
            select {
                width: 100%;
            }
        }
    }
</style>
