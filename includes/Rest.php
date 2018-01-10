<?php
namespace WeDevs\WeMail;

class Rest {

    public function __construct() {
        new \WeDevs\WeMail\Modules\Import\MailPoet\Rest();
    }

}
