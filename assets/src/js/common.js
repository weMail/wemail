import partials from './mixins/partials.js';
import routeComponent from './mixins/routeComponent.js';
import alert from './mixins/alert.js';
import Multiselect from 'vendor/vue-multiselect/src/Multiselect.vue';

// Local mixins
weMail.registerMixins({
    partials,
    routeComponent
});

// Global Mixins
weMail.Vue.mixin(alert);

// Global components
weMail.Vue.component('Multiselect', Multiselect);
