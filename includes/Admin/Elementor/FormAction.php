<?php

namespace WeDevs\WeMail\Admin\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementorPro\Modules\Forms\Classes\Action_Base;

class FormAction extends Action_Base {

    /**
     * Get action name
     *
     * @return string
     */
    public function get_name() {
        return 'wemail_form_action';
    }

    /**
     * Get action label
     *
     * @return string|void
     */
    public function get_label() {
        return __( 'weMail', 'wemail' );
    }

    /**
     * Running weMail action
     *
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run( $record, $ajax_handler ) {
        $settings = $record->get( 'form_settings' );

        if ( empty( $settings['wemail_list'] ) && ! wp_is_uuid( $settings['wemail_list'] ) ) {
            return;
        }

        if ( empty( $settings['wemail_field_maps'] ) ) {
            return;
        }

        $data = [
            'source' => 'elementor',
            'list_id' => $settings['wemail_list'],
        ];

        $field_maps = array_column( $settings['wemail_field_maps'], 'form_field_id', 'wemail_field' );
        $raw_fields = $record->get( 'fields' );

        foreach ( $field_maps as $column => $form_id ) {
            if ( isset( $raw_fields[ $form_id ]['value'] ) ) {
                $data[ $column ] = $raw_fields[ $form_id ]['value'];
            }
        }

        if ( isset( $data['email'] ) ) {
            if ( ! is_email( $data['email'] ) ) {
                return;
            }
        }

        wemail_set_owner_api_key( false );

        wemail()->api->subscribers()->put( $data );
    }

    /**
     * Register weMail action settings
     *
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section( $widget ) {
        $widget->start_controls_section(
            'section_wemail',
            [
                'label' => $this->get_label(),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'wemail_list',
            [
                'label' => __( 'weMail List ID', 'wemail' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'wemail_field',
            [
                'label' => __( 'weMail Field', 'wemail' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'email',
                'options' => [
                    'address1'          => __( 'Address 1', 'wemail' ),
                    'address2'          => __( 'Address 2', 'wemail' ),
                    'city'              => __( 'City', 'wemail' ),
                    'country'           => __( 'Country', 'wemail' ),
                    'date_of_birth'     => __( 'Date of Birth', 'wemail' ),
                    'email'             => __( 'Email', 'wemail' ),
                    'first_name'        => __( 'First Name', 'wemail' ),
                    'full_name'         => __( 'Full Name', 'wemail' ),
                    'last_name'         => __( 'Last Name', 'wemail' ),
                    'mobile'            => __( 'Mobile', 'wemail' ),
                    'phone'             => __( 'Phone', 'wemail' ),
                    'state'             => __( 'State', 'wemail' ),
                    'timezone'          => __( 'Timezone', 'wemail' ),
                    'zip'               => __( 'Zip', 'wemail' ),
                ],
                'description'   => __( 'Select column of subscriber of weMail', 'wemail' ),
            ]
        );

        $repeater->add_control(
            'form_field_id', [
                'label' => __( 'Form Field ID', 'wemail' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'email',
                'description'   => __( 'Form Fields > {Form Field} > Advanced > ID', 'wemail' ),
            ]
        );

        $widget->add_control(
            'wemail_field_maps',
            [
                'label' => __( 'Map Fields with weMail', 'wemail' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ wemail_field.split(\'_\').map((part) => part.charAt(0).toUpperCase() + part.slice(1)).join(" ") }}}',
            ]
        );

        $widget->end_controls_section();
    }

    public function on_export( $element ) {
        unset(
            $element['settings']['wemail_list'],
            $element['settings']['wemail_field_maps']
        );
    }
}
