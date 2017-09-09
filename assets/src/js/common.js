import routeComponent from './mixins/routeComponent.js';
import settings from './mixins/settings.js';
import alert from './mixins/alert.js';
import Multiselect from 'vendor/vue-multiselect/src/Multiselect.vue';
import DoAction from './components/DoAction.vue';
import foo from './components/foo.vue';
import bar from './components/bar.vue';

// Global Mixins
weMail.Vue.mixin(alert);

// Local mixins
weMail.registerMixins({
    routeComponent,
    settings
});

// Global components
weMail.Vue.component('Multiselect', Multiselect);
weMail.Vue.component('DoAction', DoAction);
weMail.Vue.component('foo', foo);
weMail.Vue.component('bar', bar);
