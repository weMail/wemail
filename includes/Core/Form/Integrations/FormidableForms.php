<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use FrmDb;
use WeDevs\WeMail\Traits\Singleton;

class FormidableForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @var string $title
     */
    public $title = 'Formidable Forms';

    /**
     * Integration slug
     *
     * @var string $slug
     */
    public $slug = 'formidable_forms';

    public function boot() {
        $this->is_active = class_exists( 'FrmHooksController' );
    }

    /**
     * Get all forms
     *
     * @return array
     */
    public function forms() {
        $forms = FrmDb::get_results( 'frm_forms', [], 'id,name' );

        if ( ! is_array( $forms ) ) {
            return [];
        }

        return array_map(
            function ( $form ) {
                return [
                    'id' => absint( $form->id ),
                    'title' => $form->name,
                    'fields' => $this->get_form_fields( $form->id ),
                ];
            },
            $forms
        );
    }

    /**
     * Get form fields
     *
     * @param $form_id
     *
     * @return array|array[]
     */
    protected function get_form_fields( $form_id ) {
        $fields = FrmDb::get_results(
            'frm_fields',
            [
                'form_id' => $form_id,
            ], 'id,name'
        );

        if ( ! is_array( $fields ) ) {
            return [];
        }

        return array_map(
            function ( $field ) {
                return [
                    'id' => $field->id,
                    'label' => $field->name,
                ];
            }, $fields
        );
    }

    /**
     * Submit data to API
     *
     * @param $data
     */
    public function submit( $data ) {
        $forms = get_option( 'wemail_form_integration_formidable_forms', [] );
        $form_id = absint( $data['form']->id );

        if ( ! in_array( $form_id, $forms, true ) ) {
            return;
        }

        $form_data = [
            'id'    => $form_id,
            'data'  => $this->get_entity_data( $data['entry_id'] ),
        ];

        if ( ! empty( $form_data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'formidable-forms' )->submit()->post( $form_data );
        }
    }

    /**
     * Get entity data
     *
     * @param $entity_id
     *
     * @return array|void
     */
    protected function get_entity_data( $entity_id ) {
        $entities = FrmDb::get_results(
            'frm_item_metas',
            [
                'item_id' => $entity_id,
            ],
            'field_id,meta_value'
        );

        if ( ! is_array( $entities ) ) {
            return [];
        }

        return array_column( $entities, 'meta_value', 'field_id' );
    }
}
