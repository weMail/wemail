<template>
    <input class="wemail-input-range" type="range" :value="value" @input="updateValue($event.target.value)">
</template>

<script>
    export default {
        props: {
            value: {
                type: Number,
                required: true
            }
        },

        methods: {
            updateValue(value) {
                this.$emit('input', parseInt(value, 10));
            }
        }
    };
</script>

<style lang="scss">
    /* generated from range.css */
    input[type="range"].wemail-input-range {
        width: 100%;
        padding: 0;
        margin: 6px 0;
        -webkit-appearance: none;

        &:focus {
            outline: none;

            &::-webkit-slider-thumb {
                background-color: #a2a2a2;
            }

            &::-moz-range-thumb {
                background-color: #a2a2a2;
            }

            &::-ms-thumb {
                background-color: #a2a2a2;
            }
        }

        &::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            background: rgba(51, 51, 51, 0.78);
            border: 0 solid rgba(177, 152, 166, 0.88);
            border-radius: 0;
            box-shadow: 0 0 0 #000, 0 0 0 #0d0d0d;
        }

        &::-webkit-slider-thumb {
            width: 16px;
            height: 16px;
            margin-top: -6px;
            cursor: pointer;
            background: #e5e5e5;
            border: 0 solid rgba(255, 255, 255, 0);
            border-radius: 8px;
            box-shadow: 0.4px 0.4px 1px #000, 0 0 0.4px #0d0d0d;
            -webkit-appearance: none;
        }

        &::-webkit-slider-runnable-track {
            background: rgba(51, 51, 51, 0.78);
        }

        &::-moz-range-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            background: rgba(51, 51, 51, 0.78);
            border: 0 solid rgba(177, 152, 166, 0.88);
            border-radius: 0;
            box-shadow: 0 0 0 #000, 0 0 0 #0d0d0d;
        }

        &::-moz-range-thumb {
            width: 16px;
            height: 16px;
            cursor: pointer;
            background: #e5e5e5;
            border: 0 solid rgba(255, 255, 255, 0);
            border-radius: 8px;
            box-shadow: 0.4px 0.4px 1px #000, 0 0 0.4px #0d0d0d;
        }

        &::-ms-track {
            width: 100%;
            height: 4px;
            color: transparent;
            cursor: pointer;
            background: transparent;
            border-color: transparent;
        }

        &::-ms-fill-lower {
            background: rgba(51, 51, 51, 0.78);
            border: 0 solid rgba(177, 152, 166, 0.88);
            border-radius: 0;
            box-shadow: 0 0 0 #000, 0 0 0 #0d0d0d;
        }

        &::-ms-fill-upper {
            background: rgba(51, 51, 51, 0.78);
            border: 0 solid rgba(177, 152, 166, 0.88);
            border-radius: 0;
            box-shadow: 0 0 0 #000, 0 0 0 #0d0d0d;
        }

        &::-ms-thumb {
            width: 16px;
            height: 4px;
            cursor: pointer;
            background: #e5e5e5;
            border: 0 solid rgba(255, 255, 255, 0);
            border-radius: 8px;
            box-shadow: 0.4px 0.4px 1px #000, 0 0 0.4px #0d0d0d;
        }

        &::-ms-fill-lower {
            background: rgba(51, 51, 51, 0.78);
        }

        &::-ms-fill-upper {
            background: rgba(51, 51, 51, 0.78);
        }
    }
</style>
