<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Core\Form\Integrations\Hooks as FormIntegrations;
use WeDevs\WeMail\Core\Sync\Subscriber\Wp\Hooks as SyncSubscriberWp;

class Hooks {

    public function __construct() {
        new SyncSubscriberWp();
        new FormIntegrations();
    }

}
