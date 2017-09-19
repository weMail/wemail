import Vue from 'vue/dist/vue.esm.js';
import VueRouter from 'vue-router/dist/vue-router.esm.js';
import Vuex from 'vuex/dist/vuex.esm.js';
import lodash from 'lodash';
import swal2 from 'sweetalert2';
import Sortable from 'sortablejs';

weMail._ = lodash;
weMail.swal2 = swal2;
weMail.Sortable = Sortable;

weMail.Vue = Vue;
weMail.Vuex = Vuex;
weMail.VueRouter = VueRouter;

weMail.Vue.use(weMail.VueRouter);
weMail.Vue.use(weMail.Vuex);
