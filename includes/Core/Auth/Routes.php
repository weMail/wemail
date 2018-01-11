<?php

namespace WeDevs\WeMail\Core\Auth;

use WeDevs\WeMail\Traits\Hooker;

class Routes {

    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_get_route_data_authSite', 'auth_site', 10, 2 );
    }

    /**
     * Data for the route authSite
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function auth_site() {
        return [
        ];
    }

}
