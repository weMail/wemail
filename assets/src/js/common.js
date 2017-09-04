import partials from './mixins/partials.js';
import routeComponent from './mixins/routeComponent.js';
import Multiselect from 'vendor/vue-multiselect/src/Multiselect.vue';

weMail.registerMixins({
    partials,
    routeComponent
});

weMail.Vue.component('Multiselect', Multiselect);
