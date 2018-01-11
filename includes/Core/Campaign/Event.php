<?php
namespace WeDevs\WeMail\Core\Campaign;

class Event {

    public function __construct() {
    }

    public function all() {
        return apply_filters( 'wemail_campaign_events', [
            [
                'action'      => 'wemail_subscribed_to_list',
                'actionTitle' => __( 'when someone subscribes to the list', 'wemail' ),
                'options'     => wemail()->lists->items()
            ],
            [
                'action'      => 'wemail_matches_segment',
                'actionTitle' => __( 'when a subscriber added to the segment', 'wemail' ),
                'options'     => wemail()->segment->all()
            ]
        ] );
    }

}
