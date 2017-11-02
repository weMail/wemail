import Vue from 'vue/dist/vue.esm.js';
import VueRouter from 'vue-router/dist/vue-router.esm.js';
import Vuex from 'vuex/dist/vuex.esm.js';
import _ from 'lodash';
import swal2 from 'sweetalert2';
import Sortable from 'sortablejs';

Vue.config.productionTip = !weMail.turnOffVueProductionTip;

weMail.Vue = Vue;
weMail.Vuex = Vuex;
weMail.VueRouter = VueRouter;

weMail.Vue.use(weMail.VueRouter);
weMail.Vue.use(weMail.Vuex);

weMail._ = _.noConflict();
weMail.swal2 = swal2;
weMail.Sortable = Sortable;
