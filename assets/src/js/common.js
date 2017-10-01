import routeComponent from './mixins/routeComponent.js';
import settings from './mixins/settings.js';
import alert from './mixins/alert.js';
import Multiselect from 'vue-multiselect/src/Multiselect.vue';
import DoAction from './components/DoAction.vue';
import TextEditor from './components/TextEditor.vue';
import ColorPicker from './components/ColorPicker.vue';
import InputRange from './components/InputRange.vue';
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
weMail.Vue.component('TextEditor', TextEditor);
weMail.Vue.component('ColorPicker', ColorPicker);
weMail.Vue.component('InputRange', InputRange);
weMail.Vue.component('foo', foo);
weMail.Vue.component('bar', bar);
