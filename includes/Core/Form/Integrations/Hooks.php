<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Core\Form\Integrations\ContactForm7;
use WeDevs\WeMail\Core\Form\Integrations\GravityForms;

class Hooks {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'wpcf7_submit', [ContactForm7::instance(), 'submit'], 10, 2 );
        add_action( 'gform_after_submission', [GravityForms::instance(), 'submit'], 10, 2 );
        add_action( 'wpforms_process_complete', [Wpforms::instance(), 'submit'], 10, 4 );
    }
}
