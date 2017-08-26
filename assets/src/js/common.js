import partials from './mixins/partials.js';
import select2 from './components/select2.vue';

weMail.registerMixins({
    partials
});

weMail.Vue.component('select2', select2);
