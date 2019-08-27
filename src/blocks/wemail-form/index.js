const { registerBlockType } = wp.blocks;
const { __ }  = wp.i18n;
const { SelectControl } = wp.components;
const { RawHTML } = wp.element;
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
    edit(props) {

        function generateShortcode(formId){
            return `[wemail_form id="${formId}"]`;
        }

        function updateFormId(value) {
            props.setAttributes({
                shortcode: generateShortcode(value),
                formId: value
            });
        }

        const options = [{
            label: __('Select your form'),
            value: ''
        }];

        weMailData.forms.forEach( form => {
            options.push({
                label: form.name,
                value: form.id
            });
        });
        return (
            <div className="wemail-block">
                <div className="icon">
                    {icon({ color: true})}
                </div>
                <h4 className="title">{ __('weMail Form') }</h4>
                <SelectControl value={props.attributes.formId}
                onChange={updateFormId}
                label={__('Forms')}
                options={options}/>
            </div>
        );
    },

    save(props) {
        return <RawHTML>{ props.attributes.shortcode }</RawHTML>;
    }
});
