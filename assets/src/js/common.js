import routeComponent from './mixins/routeComponent.js';
import settings from './mixins/settings.js';
import alert from './mixins/alert.js';
import dataValidators from './mixins/dataValidators.js';
import Multiselect from 'vue-multiselect/src/Multiselect.vue';
import DoAction from './components/DoAction.vue';
import TextEditor from './components/TextEditor.vue';
import ColorPicker from './components/ColorPicker.vue';
import InputRange from './components/InputRange.vue';
import Datepicker from './components/Datepicker.vue';
import Timepicker from './components/Timepicker.vue';

// Global Mixins
Vue.mixin(alert);

// Local mixins
weMail.registerMixins({
    routeComponent,
    settings,
    dataValidators
});

// Global components
Vue.component('Multiselect', Multiselect);
Vue.component('DoAction', DoAction);
Vue.component('TextEditor', TextEditor);
Vue.component('ColorPicker', ColorPicker);
Vue.component('InputRange', InputRange);
Vue.component('Datepicker', Datepicker);
Vue.component('Timepicker', Timepicker);
