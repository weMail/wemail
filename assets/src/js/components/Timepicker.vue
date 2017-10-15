<template>
    <input
        type="text"
        :value="formattedValue"
        :placeholder="placeholder"
        @input="updateValue($event.target.value)"
    >
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: true,
                default: ''
            },

            placeholder: {
                type: String,
                required: false,
                default: ''
            },

            changeMonthYear: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        computed: {
            formattedValue() {
                return weMail.dateTime.toMoment(weMail.dateTime.server.timeFormat, moment(this.value, 'HH:mm:ss'));
            }
        },

        mounted() {
            const vm = this;

            $(vm.$el).timepicker({
                timeFormat: weMail.dateTime.server.timeFormat,
                scrollDefault: 'now',
                step: 15,
                minTime: '12:00 am',
                maxTime: '24 hours after minTime'
            });

            $(vm.$el).on('changeTime', function () {
                vm.updateValue($(this).val());
            });
        },

        methods: {
            updateValue(value) {
                if (!value) {
                    value = moment().format('HH:mm:ss');
                } else {
                    value = moment(value, weMail.momentTimeFormat).format('HH:mm:ss');
                }

                this.$emit('input', value);
            }
        }
    };
</script>
