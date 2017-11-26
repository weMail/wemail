import routeComponent from './mixins/routeComponent.js';
import settings from './modules/Settings/mixins/settings.js';
import alert from './mixins/alert.js';
import i18n from './mixins/i18n.js';
import dataValidators from './mixins/dataValidators.js';
import helpers from './mixins/helpers.js';
import imageUploader from './mixins/imageUploader.js';
import Multiselect from 'vue-multiselect/src/Multiselect.vue';
import DoAction from './components/DoAction.vue';
import RenderHtml from './components/RenderHtml.vue';
import TextEditor from './components/TextEditor.vue';
import ColorPicker from './components/ColorPicker.vue';
import InputRange from './components/InputRange.vue';
import Datepicker from './components/Datepicker.vue';
import Timepicker from './components/Timepicker.vue';
import ListsDropdown from './components/ListsDropdown.vue';
import InlineEditor from './components/InlineEditor.vue';

// Global Mixins
Vue.mixin(alert);
Vue.mixin(i18n);

// Local mixins
weMail.registerMixins({
    routeComponent,
    settings,
    dataValidators,
    helpers,
    imageUploader
});

// Global components
Vue.component('Multiselect', Multiselect);
Vue.component('DoAction', DoAction);
Vue.component('RenderHtml', RenderHtml);
Vue.component('TextEditor', TextEditor);
Vue.component('ColorPicker', ColorPicker);
Vue.component('InputRange', InputRange);
Vue.component('Datepicker', Datepicker);
Vue.component('Timepicker', Timepicker);
Vue.component('ListsDropdown', ListsDropdown);
Vue.component('InlineEditor', InlineEditor);

// Lazy loaded components
__webpack_public_path__ = `${weMail.assetsURL}/js/`; // eslint-disable-line camelcase

function ListTable(resolve) {
    require.ensure(['./components/ListTable.vue'], () => {
        resolve(require('./components/ListTable.vue')); // eslint-disable-line global-require
    });
}

weMail.registerComponents({
    ListTable
});
