import { registerBlockType } from '@wordpress/blocks'
import { __ } from '@wordpress/i18n';
import { SelectControl, PanelBody, Spinner } from '@wordpress/components';
import { RawHTML, Fragment } from '@wordpress/element'
import { InspectorControls } from '@wordpress/block-editor';
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
        },
        isLoading: {
            type: 'boolean',
            default: false
        }
    },
    edit(props) {

        function generateShortcode(formId) {
            return `[wemail_form id="${formId}"]`;
        }

        function updateFormId(value) {
            props.setAttributes({
                shortcode: generateShortcode(value),
                formId: value,
                isLoading: true
            });
        }

        function loadForm(event) {
            props.setAttributes({
                isLoading: false
            });

            let iframe = event.target;
            iframe.removeAttribute('height');

            iframe.height = iframe.contentWindow.document.body.offsetHeight;
        }

        function previewForm(props) {
            return (
                <div height="500px" className="wemail-block-form-preview">
                    <div className="wemail-block-overlay"/>
                    <iframe className={props.attributes.isLoading ? 'hide' : ''} onLoad={loadForm} width="100%"
                            src={`${window.weMailData.siteUrl}/wp-admin/admin-ajax.php?action=wemail_preview&form_id=${props.attributes.formId}`}
                            frameBorder="0" scrolling="no"/>
                    {
                        props.attributes.isLoading ?
                            <Spinner/>
                            : null
                    }
                </div>
            );
        }

        function defaultPreview(props, options) {
            return (
                <Fragment>
                    <div className="icon">
                        {icon({color: true})}
                    </div>
                    <h2 className="title">{__('weMail Form')}</h2>
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
            disabled: false
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
