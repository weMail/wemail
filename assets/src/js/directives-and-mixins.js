import datepicker from './directives/datepicker.js';
import partials from './mixins/partials';

weMail.Vue.directive('wemail-datepicker', datepicker);

weMail.registerMixins({
    partials
});
