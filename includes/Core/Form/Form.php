<?php

namespace WeDevs\WeMail\Core\Form;

use WeDevs\WeMail\Traits\Singleton;

class Form {

    use Singleton;

    /**
     * Get paginated list of forms
     *
     * @since 1.0.0
     *
     * @param array $query
     *
     * @return array
     */
    public function all( $query ) {
        return wemail()->api->forms()->query( $query )->get();
    }

    /**
     * Get a single form data
     *
     * @since 1.0.0
     *
     * @param string $id
     *
     * @return array
     */
    public function get( $id ) {
        return wemail()->api->forms($id)->get();
    }
}
