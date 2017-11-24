<?php

namespace WeDevs\WeMail\Modules\Auth;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Routes {

    use Hooker;

    public function __construct() {
        $this->add_filter( 'wemail_get_route_data_authSite', 'auth_site', 10, 2 );
    }

    public function auth_site() {
        return [
            'i18n' => [
                'connectWeMail' => __( 'Connect weMail', 'wemail' )
            ]
        ];
    }

}
