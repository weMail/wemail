<template>
    <multiselect
        :value="selectedLists"
        :options="lists"
        :multiple="true"
        :close-on-select="false"
        :preserve-search="true"
        :custom-label="customLabel"
        track-by="id"
        label="name"
        :placeholder="i18n.selectLists"
        @select="select"
        @remove="remove"
    >
        <template slot="option" slot-scope="props">{{ props.option.name }}</template>
        <span slot="noResult">{{ i18n.noListFound }}</span>
    </multiselect>
</template>

<script>
    export default {
        props: {
            value: {
                type: Array,
                required: true
            }
        },

        computed: {
            lists() {
                return this.$store.state.global.lists;
            },

            i18n() {
                return this.$store.state.global.i18n;
            },

            selectedLists() {
                const lists = [];

                let i = 0;

                for (i = 0; i < this.lists.length; i++) {
                    if (this.value.indexOf(this.lists[i].id) >= 0) {
                        lists.push(this.lists[i]);
                    }
                }

                return lists;
            }
        },

        methods: {
            customLabel(option) {
                return _.truncate(option.name, {
                    length: 15
                });
            },

            select(selectedList) {
                const lists = $.extend(true, [], this.value);

                lists.push(selectedList.id);

                this.$emit('input', lists);
            },

            remove(removedList) {
                const lists = $.extend(true, [], this.value);
                const index = lists.indexOf(removedList.id);

                if (index >= 0) {
                    lists.splice(index, 1);

                    this.$emit('input', lists);
                }
            }
        }
    };
</script>
