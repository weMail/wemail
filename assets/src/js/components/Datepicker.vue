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

        data() {
            return {
                formatMap: {
                    // Day
                    d: 'dd',
                    D: 'D',
                    j: 'd',
                    l: 'DD',

                    // Month
                    F: 'MM',
                    m: 'mm',
                    M: 'M',
                    n: 'm',

                    // Year
                    o: 'yy', // not exactly same. see php date doc for details
                    Y: 'yy',
                    y: 'y'
                }
            };
        },

        computed: {
            formattedValue() {
                return weMail.dateTime.toMoment(weMail.dateTime.server.dateFormat, this.value);
            }
        },

        mounted() {
            const vm = this;
            const wpDateFormat = weMail.dateTime.server.dateFormat;

            let i = 0;
            let char = '';
            let datepickerFormat = '';

            for (i = 0; i < wpDateFormat.length; i++) {
                char = wpDateFormat[i];

                if (char in vm.formatMap) {
                    datepickerFormat += vm.formatMap[char];
                } else {
                    datepickerFormat += char;
                }
            }

            $(vm.$el).datepicker({
                dateFormat: datepickerFormat,
                changeMonth: vm.changeMonthYear,
                changeYear: vm.changeMonthYear,
                firstDay: weMail.dateTime.server.startOfWeek,

                beforeShow() {
                    $(this).datepicker('widget').addClass('wemail-datepicker');
                },

                onSelect(date) {
                    vm.updateValue(date);
                }
            });
        },

        methods: {
            updateValue(value) {
                if (!value) {
                    value = moment().format('YYYY-MM-DD');
                } else {
                    value = moment(value, weMail.momentDateFormat).format('YYYY-MM-DD');
                }
                this.$emit('input', value);
            }
        }
    };
</script>
