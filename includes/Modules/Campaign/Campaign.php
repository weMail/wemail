<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;
use WeDevs\WeMail\Modules\Campaign\Event;
use WeDevs\WeMail\Modules\Campaign\Editor;

class Campaign extends Module {

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var WeDevs\WeMail\Modules\Campaign\Event
     */
    public $event;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var WeDevs\WeMail\Modules\Campaign\Editor
     */
    public $editor;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->event  = new Event();
        $this->editor = new Editor();
    }

    // this will remove after finishing the templating system
    public function testTemplate( $campaign ) {
        $campaign['email']['template'] = wemail()->api->templates('basic')->get();

        return $campaign;
    }

    /**
     * Get a collection of campaigns
     *
     * @since 1.0.0
     *
     * @param array $query
     *
     * @return array
     */
    public function all( $query = [] ) {
        return wemail()->api->campaigns()->query( $query )->get();
    }

    /**
     * Get a single campaign
     *
     * @since 1.0.0
     *
     * @param string $id
     * @param array  $query
     *
     * @return array
     */
    public function get( $id, $query = [] ) {
        $campaign = wemail()->api->campaigns( $id )->query( $query )->get();

        if ( isset( $campaign['data'] ) ) {
            $campaign = $campaign['data'];

            if ( empty( $campaign['email']['template'] ) ) {
                $campaign['email']['template'] = function () {};
            }

        } else {
            $campaign = null;
        }

        return $campaign;
    }

}
