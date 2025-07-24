<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\Core\Form\Integrations\Rest as FormIntegrations;
use WeDevs\WeMail\Rest\Ecommerce\Ecommerce;
use WeDevs\WeMail\Rest\Affiliate\Integrations as AffiliateIntegrations;
use WeDevs\WeMail\Rest\Help\Help;
use WeDevs\WeMail\Traits\Hooker;

class Rest {

    use Hooker;

    public function __construct() {
        $this->add_action( 'rest_api_init', 'register_controllers' );
    }

    public function register_controllers() {
        new Countries();
        new Pages();
        new States();
        new Customizer();
        new Auth();
        new MailPoet();
        new Csv();
        new Forms();
        new Video();
        new WP();
        new Site();
        new FormIntegrations();
        new ERP();
        new Pages();
        new Users();
        new Ecommerce();
        new Help();
        new AffiliateIntegrations();
        new Remote();
    }
}
