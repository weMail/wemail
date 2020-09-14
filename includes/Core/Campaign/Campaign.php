<?php

namespace WeDevs\WeMail\Core\Campaign;

use WeDevs\WeMail\Core\Campaign\Editor;
use WeDevs\WeMail\Core\Campaign\Event;
use WeDevs\WeMail\Traits\Singleton;

class Campaign {

    use Singleton;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var WeDevs\WeMail\Core\Campaign\Event
     */
    public $event;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var WeDevs\WeMail\Core\Campaign\Editor
     */
    public $editor;

    /**
     * Executes during instance creation
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        $this->event  = new Event();
        $this->editor = new Editor();
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
    public function get( $id, $include = [] ) {
        $campaign = wemail()->api->campaigns( $id );

        if ( ! empty( $include ) ) {
            $campaign = $campaign->query( [ 'include' => implode( ',', $include ) ] );
        }

        $campaign = $campaign->get();

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
