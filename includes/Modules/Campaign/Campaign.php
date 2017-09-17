<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;
use WeDevs\WeMail\Modules\Campaign\Event;

class Campaign extends Module {

    private $menu_priority = 2;

    public $event;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );

        $this->event = new Event();
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];

        return $menu_items;
    }

    public function index( $params, $query ) {
        return [
            'i18n' => [
                'campaigns' => __( 'Campaigns', 'wemail' )
            ],
            'campaigns' => wemail()->api->campaigns()->query( $query )->get()
        ];
    }

    public function create( $params, $query ) {
        return [
            'i18n' => [
                'createCampaign'    => __( 'Create Campaign', 'wemail' ),
                'campaignName'      => __( 'Campaign Name', 'wemail' ),
                'campaignNameHint'  => __( 'Enter a name to help you remember what this campaign is all about. Only you will see this.', 'wemail' ),
                'campaignType'      => __( 'Campaign Type', 'wemail' ),
                'standard'          => __( 'Standard', 'wemail' ),
                'automatic'         => __( 'Automatic', 'wemail' ),
                'subscribers'       => __( 'Subscribers', 'wemail' ),
                'lists'             => __( 'Lists', 'wemail' ),
                'createNewList'     => __( 'Create new list', 'wemail' ),
                'segments'          => __( 'Segments', 'wemail' ),
                'createNewSegment'  => __( 'Create new segment', 'wemail' ),
                'create'            => __( 'Create', 'wemail' ),
                'selectLists'       => __( 'Select Lists', 'wemal' ),
                'selectSegments'    => __( 'Select Segments', 'wemal' ),
                'setup'             => __( 'Setup', 'wemail' ),
                'template'          => __( 'Template', 'wemail' ),
                'design'            => __( 'Design', 'wemail' ),
                'send'              => __( 'Send', 'wemail' )
            ],
            'lists' => wemail()->lists->all(),
            'segments' => wemail()->segment->all()
        ];
    }

    public function show( $params, $query ) {
        return [
            'i18n' => [
                'campaign' => __( 'List', 'wemail' )
            ],
            'campaign' => $this->get( $params['id'] )
        ];
    }

    public function edit( $params, $query ) {
        return [
            'i18n' => [
                'editCampaign'           => __( 'Edit Campaign', 'wemail' ),
                'campaignName'           => __( 'Campaign Name', 'wemail' ),
                'campaignNameHint'       => __( 'Enter a name to help you remember what this campaign is all about. Only you will see this.', 'wemail' ),
                'campaignType'           => __( 'Campaign Type', 'wemail' ),
                'standard'               => __( 'Standard', 'wemail' ),
                'automatic'              => __( 'Automatic', 'wemail' ),
                'subscribers'            => __( 'Subscribers', 'wemail' ),
                'lists'                  => __( 'Lists', 'wemail' ),
                'segments'               => __( 'Segments', 'wemail' ),
                'selectLists'            => __( 'Select Lists', 'wemal' ),
                'selectSegments'         => __( 'Select Segments', 'wemal' ),
                'setup'                  => __( 'Setup', 'wemail' ),
                'template'               => __( 'Template', 'wemail' ),
                'design'                 => __( 'Design', 'wemail' ),
                'send'                   => __( 'Send', 'wemail' ),
                'next'                   => __( 'Next', 'wemail' ),
                'previous'               => __( 'Previous', 'wemail' ),
                'automaticallySend'      => __( 'Automatically Send', 'wemail' ),
                'noOptionFoundForAction' => __( 'no option found for this action', 'wemail' ),
                'immediately'            => __( 'Immediately', 'wemail' ),
                'hoursAfter'             => __( 'hour(s) after', 'wemail' ),
                'daysAfter'              => __( 'day(s) after', 'wemail' ),
                'weeksAfter'             => __( 'week(s) after', 'wemail' ),
            ],
            'campaign'   => $this->get( $params['id'] ),
            'lists'      => wemail()->lists->all(),
            'segments'   => wemail()->segment->all(),
            'events'     => $this->event->all(),
        ];
    }

    public function get( $id ) {
        $campaign = wemail()->api->campaigns( $id )->get();

        if ( isset( $campaign['data'] ) ) {
            $campaign = $campaign['data'];
        } else {
            $campaign = null;
        }

        return $campaign;
    }

}
