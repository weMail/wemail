<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Core\Form\Integrations\CalderaForms;
use WeDevs\WeMail\Core\Form\Integrations\ContactForm7;
use WeDevs\WeMail\Core\Form\Integrations\GravityForms;
use WeDevs\WeMail\Core\Form\Integrations\NinjaForms;
use WeDevs\WeMail\Core\Form\Integrations\Weforms;
use WeDevs\WeMail\Core\Form\Integrations\Wpforms;

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
    }
}
