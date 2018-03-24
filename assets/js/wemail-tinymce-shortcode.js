;(function($) {
    'use strict';

    tinymce.create('tinymce.plugins.wemail_forms_button', {
        init : function(editor, url) {
            var menuItems = wemail_forms_shortcode_button.forms.map(function (form) {
                return {
                    text: form.name,
                    onclick: function () {
                        editor.insertContent('[wemail_form id="' + form.id + '"]');
                    }
                };
            });

            editor.addButton('wemail_forms_button', {
                title : wemail_forms_shortcode_button.title,
                image: wemail_forms_shortcode_button.icon,
                type: 'menubutton',
                menu: menuItems
            });
        },
    });

    tinymce.PluginManager.add('wemail_forms_button', tinymce.plugins.wemail_forms_button);

})(jQuery);
