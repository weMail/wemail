<?php

namespace WeDevs\WeMail\Core\Campaign;

use WeDevs\WeMail\Traits\Singleton;

/**
 * @property Editor $editor
 */
class Campaign {

    use Singleton;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var \WeDevs\WeMail\Core\Campaign\Event
     */
    public $event;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var \WeDevs\WeMail\Core\Campaign\Editor
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
    public function all( $query = array() ) {
        return wemail()->api->campaigns()->query( $query )->get();
    }

    /**
     * Get a single campaign
     *
     * @param string $id
     * @param array $include
     * @return array
     * @since 1.0.0
     */
    public function get( $id, $include = array() ) {
        $campaign = wemail()->api->campaigns( $id );

        if ( ! empty( $include ) ) {
            $campaign = $campaign->query( array( 'include' => implode( ',', $include ) ) );
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
