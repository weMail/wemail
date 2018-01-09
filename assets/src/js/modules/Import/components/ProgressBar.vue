<template>
    <div class="import-progress-bar">
        <ul class="list-inline">
            <router-link
                v-for="(route, index) in routes"
                :key="route.name"
                :to="{name: route.name}"
                tag="li"
                :class="['list-inline-item', stepClass[index]]"
                active-class="active"
                exact
                :style="calcWidth"
            >
                <a>{{ route.title }}</a>
            </router-link>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            routes: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                currentRouteName: ''
            };
        },

        computed: {
            calcWidth() {
                const items = this.routes.length;
                const width = parseFloat(100 / items).toFixed(2); // eslint-disable-line no-magic-numbers

                return {
                    width: `calc(${width}% - (${items} * 5px))`
                };
            },

            stepClass() {
                const currentStep = _.findIndex(this.routes, {
                    name: this.currentRouteName
                });

                const classNames = [];

                this.routes.forEach((route, index) => {
                    if (index <= currentStep) {
                        classNames.push('step-done');
                    } else {
                        classNames.push('');
                    }
                });

                return classNames;
            }
        },

        created() {
            this.currentRouteName = this.$router.currentRoute.name;
        },

        watch: {
            $route(to) {
                this.currentRouteName = to.name;
            }
        }
    };
</script>

<style lang="scss" scoped>
    .import-progress-bar {
        margin-bottom: 30px;
        overflow: hidden;
        text-align: center;
        counter-reset: step;

        li {
            position: relative;
            float: left;
            width: 33.33%;
            font-size: 15px;
            font-weight: 300;
            color: #fff;
            list-style-type: none;

            &:before {
                display: block;
                width: 25px;
                margin: 0 auto 5px;
                font-size: 10px;
                line-height: 25px;
                color: #333;
                content: counter(step);
                counter-increment: step;
                background: #fff;
                border-radius: 50%;
                box-shadow: 0 0 1px 1px #8e8e8e;
            }

            &:after {
                position: absolute;
                top: 9px;
                left: -50%;
                z-index: -1;
                width: 100%;
                height: 2px;
                content: "";
                background: #dadada;
            }

            &:first-child:after {
                content: none;
            }

            &.step-done:before {
                box-shadow: 0 0 1px 1px $wp-blue;
            }

            &.step-done:before,
            &.step-done:after {
                color: #fff;
                background: $wp-blue;
            }

            a {
                text-decoration: none;

                &:focus,
                &:active {
                    box-shadow: none;
                }
            }
        }
    }
</style>
