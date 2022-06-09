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
        add_action( 'wpcf7_submit', [ ContactForm7::instance(), 'submit' ], 999, 2 );
        add_action( 'gform_after_submission', [ GravityForms::instance(), 'submit' ], 999, 2 );
        add_action( 'wpforms_process_complete', [ Wpforms::instance(), 'submit' ], 999, 4 );
        add_action( 'caldera_forms_submit_complete', [ CalderaForms::instance(), 'submit' ], 999, 4 );
        add_action( 'weforms_entry_submission', [ Weforms::instance(), 'submit' ], 999, 4 );
        add_action( 'nf_save_sub', [ NinjaForms::instance(), 'submit' ], 999, 1 );
        add_action( 'fluentform_before_insert_submission', [ FluentForms::instance(), 'submit' ], 999, 1 );
        add_action( 'happyforms_submission_success', [ HappyForms::instance(), 'submit' ], 999, 1 );
        add_action( 'frm_after_entry_processed', [ FormidableForms::instance(), 'submit' ], 999, 1 );
        add_action( 'wp_ajax_sgpb_process_after_submission', [ PopupBuilder::instance(), 'submit' ], 999, 1 );
        add_action( 'pum_sub_form_success', [ PopupMaker::instance(), 'submit' ], 999, 1 );
        add_action( 'forminator_custom_form_submit_before_set_fields', [ ForminatorForms::instance(), 'submit' ], 999, 3 );
        add_action( 'everest_forms_process', [ EverestForms::instance(), 'submit' ], 999, 3 );
    }
}
