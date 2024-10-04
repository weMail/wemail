<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

class Hooks {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'wpcf7_submit', array( ContactForm7::instance(), 'submit' ), 999, 2 );
        add_action( 'gform_after_submission', array( GravityForms::instance(), 'submit' ), 999, 2 );
        add_action( 'wpforms_process_complete', array( Wpforms::instance(), 'submit' ), 999, 4 );
        add_action( 'caldera_forms_submit_complete', array( CalderaForms::instance(), 'submit' ), 999, 4 );
        add_action( 'weforms_entry_submission', array( Weforms::instance(), 'submit' ), 999, 4 );
        add_action( 'nf_save_sub', array( NinjaForms::instance(), 'submit' ), 999, 1 );
        add_action( 'fluentform_before_insert_submission', array( FluentForms::instance(), 'submit' ), 999, 1 );
        add_action( 'happyforms_submission_success', array( HappyForms::instance(), 'submit' ), 999, 1 );
        add_action( 'frm_after_entry_processed', array( FormidableForms::instance(), 'submit' ), 999, 1 );
        add_action( 'wp_ajax_sgpb_process_after_submission', array( PopupBuilder::instance(), 'submit' ), 999, 1 );
        add_action( 'pum_sub_form_success', array( PopupMaker::instance(), 'submit' ), 999, 1 );
        add_action( 'forminator_custom_form_submit_before_set_fields', array( ForminatorForms::instance(), 'submit' ), 999, 3 );
        add_action( 'everest_forms_process', array( EverestForms::instance(), 'submit' ), 999, 3 );
        add_action( 'elementor_pro/forms/new_record', array( ElementorForms::instance(), 'submit' ), 999, 3 );
    }
}
