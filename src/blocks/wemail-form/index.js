const { registerBlockType } = wp.blocks;
const { __ }  = wp.i18n;
const { SelectControl, PanelBody } = wp.components;
const { RawHTML, Fragment } = wp.element;
const { InspectorControls } = wp.editor;
import { icon } from './icon'


registerBlockType('wemail/forms', {
    title: __('weMail'),
    description: __('Here you can add your weMail form.'),
    category: 'common',
    icon,
    keywords: [ __('forms'), __('mail') ],
    attributes: {
        formId: {
            type: 'string'
        },
        shortcode: {
            type: 'string'
        }
    },
    edit: function (props) {

        function generateShortcode(formId) {
            return `[wemail_form id="${formId}"]`;
        }

        function updateFormId(value) {
            props.setAttributes({
                shortcode: generateShortcode(value),
                formId: value
            });
        }

        function previewForm(props) {
            return (
                <div height="500px" class="wemail-block-form-preview">
                    <div class="wemail-block-overlay"></div>
                    <iframe height="500px" width="100%" src={`${window.origin}?wemail_form=${props.attributes.formId}`}
                            frameBorder="0" scrolling="no"></iframe>
                </div>
            );
        }

        function defaultPreview(props, options) {
            return (
                <Fragment>
                    <div className="icon">
                        {icon({color: true})}
                    </div>
                    <h4 className="title">{__('weMail Form')}</h4>
                    <SelectControl value={props.attributes.formId}
                                   onChange={updateFormId}
                                   label={__('Forms')}
                                   options={options}/>
                </Fragment>
            );
        }

        const options = [{
            label: __('Select your form'),
            value: '',
            disabled: true
        }];

        weMailData.forms.forEach(form => {
            options.push({
                label: form.name,
                value: form.id
            });
        });
        return (
            <div className="wemail-block">
                {
                    <InspectorControls>
                        <PanelBody title={__('Forms')}>
                            <SelectControl label={__('Select your form')} value={props.attributes.formId}
                                           onChange={updateFormId}
                                           options={options}/>
                        </PanelBody>
                    </InspectorControls>
                }

                {
                    props.attributes.formId ? previewForm(props) : defaultPreview(props, options)
                }
            </div>
        );
    },

    save(props) {
        return <RawHTML>{ props.attributes.shortcode }</RawHTML>;
    }
});
