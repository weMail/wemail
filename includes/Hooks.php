<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Core\Form\Integrations\Hooks as FormIntegrations;
use WeDevs\WeMail\Core\Mail\Hooks as MailHooks;
use WeDevs\WeMail\Core\Sync\Subscriber\Erp\Hooks as SyncSubscriberErp;
use WeDevs\WeMail\Core\Sync\Subscriber\Wp\Hooks as SyncSubscriberWp;
use WeDevs\WeMail\Core\User\Integrations\WpUser as SyncWpUser;
use WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce\Orders as SyncWCOrders;
use WeDevs\WeMail\Core\Sync\Ecommerce\EDD\Orders as SyncEDDOrders;
use WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce\Products as SyncWCProducts;

class Hooks {

    public function __construct() {
        new FormIntegrations();
        new SyncSubscriberWp();
        new SyncSubscriberErp();
        new SyncWpUser();
        new MailHooks();
        new SyncWCOrders();
        new SyncEDDOrders();
        new SyncWCProducts();
    }

}
