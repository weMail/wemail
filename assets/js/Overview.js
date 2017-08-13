weMail.registerStore('overview', {
    mutations: {
        setStoreStates: function (state, payload) {
            console.log(state);
            console.log(payload);
            // state = payload;
            // console.log(this);

            // state.modelA = payload.modelA;

            _lodash.forEach(payload, function (val, key) {
                state[key] = val;

                // Vue.set(state[key], val);
            });



            // state = jQuery.extend(true, payload, state);

            // Vue.set(state, payload);
        },

        setModelA: function (state, payload) {
            state.modelA = payload;
        },

        setModelB: function (state, payload) {
            state.modelB = payload;
        },

        setPlainState: function (state, payload) {
            state[payload.state] = payload.value;
        },
    }
});

weMail.routeComponents.Overview = {
    template: `
        <div>
            <p>Overview component</p>
            directive <pre>{{ startDate }}</pre>
            <p><input type="text" v-model="startDate" v-wemail-datepicker="startDate" id="hook-arguments-example"></p>

            echo <pre>{{ startDate }}</pre>
            <p><datepicker v-model="startDate"></datepicker></p>
            <hr>
            models: {{ modelA }} | {{ modelB }}
            <h3>comp-a</h3>
            <comp-a></comp-a>
            <h3>comp-b</h3>
            <comp-b></comp-b>

            <partial-overview></partial-overview>
        </div>
    `,
            // <pre>{{ modelNested }}</pre>

    store: new Vuex.Store(weMail.stores.overview),

    data() {
        return {
            message: 'this is message string',
            buttonLabel: 'this is button label',
            datePicker: '',
            startDate: '2017-08-12'
        }
    },

    mounted: function () {
        // var vm = this;

        // weMail.ajax.get('get_overview_initial_data', {
        //     param1: 'param1',
        //     param2: 'param2'
        // }).done(function (response) {
        //    vm.$store.commit('setStoreStates', response.data);
        // });
    },

    computed: {
        modelA: function () {
            return this.$store.state.modelA;
        },

        modelB: function () {
            return this.$store.state.modelB;
        },

        // modelNested: function () {
        //     return this.$store.state.modelNested;
        // }
        // ...Vuex.mapState(['modelA']),

        // modelA: {

        // }
    }
};


weMail.component('partial-overview', {
    mixins: [weMail.mixins.partials],

    computed: {
        partials: function () {
            return weMail.partials.overview || [];
        }
    },

    // name:

    data: function () {
        return {

        }
    }
});

weMail.component('comp-a', {
    template: `
        <div>
            <input type="text" v-model="compAModelA"> <input type="text" v-model="compAModelB">
            <p>Model Nested</p>
        </div>
    `,
            // <input type="text" v-model="one">
            // <input type="text" v-model="two">
            // <input type="text" v-model="three">

    computed: {
        compAModelA: {
            get: function () {
                return this.$store.state.modelA;
            },

            set: function (val) {
                this.$store.commit('setModelA', val);
            }
        },

        // compAModelA: function () {
        //     return this.$store.state.modelA;
        // },


        compAModelB: {
            get: function () {
                return this.$store.state.modelB;
            },

            set: function (val) {
                this.$store.commit('setModelB', val);
            }
        },

        compAModelNested: {
            get: function () {
                return this.$store.state.modelNested;
            },

            set: function (val) {
                this.$store.commit('setModelNested', val);
            }
        },
    }
});


weMail.component('comp-b', {
    template: `
        <input type="text" v-model="compBModelA">
    `,

    computed: {
        compBModelA: {
            get: function () {
                return this.$store.state.modelA;
            },

            set: function (val) {
                this.$store.commit('setModelA', val);
            }
        }
    }
});
