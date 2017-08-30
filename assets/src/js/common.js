import partials from './mixins/partials.js';
import Multiselect from 'vendor/vue-multiselect/src/Multiselect.vue';

weMail.registerMixins({
    partials
});

weMail.Vue.component('Multiselect', Multiselect);
