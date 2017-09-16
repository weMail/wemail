<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;

class Campaign extends Module {

    private $menu_priority = 2;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );
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
            'campaigns' => $this->get_campaigns( $query )
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
            'lists' => wemail()->api->get('/lists/all'),
            'segments' => wemail()->api->get('/segments/all'),
        ];
    }

    public function show( $params, $query ) {
        return [
            'i18n' => [
                'campaign' => __( 'List', 'wemail' )
            ],
            'campaign' => $this->get_campaign( $params['id'] )
        ];
    }

    public function edit( $params, $query ) {
        return [
            'i18n' => [
                'editCampaign'      => __( 'Edit Campaign', 'wemail' ),
                'campaignName'      => __( 'Campaign Name', 'wemail' ),
                'campaignNameHint'  => __( 'Enter a name to help you remember what this campaign is all about. Only you will see this.', 'wemail' ),
                'campaignType'      => __( 'Campaign Type', 'wemail' ),
                'standard'          => __( 'Standard', 'wemail' ),
                'automatic'         => __( 'Automatic', 'wemail' ),
                'subscribers'       => __( 'Subscribers', 'wemail' ),
                'lists'             => __( 'Lists', 'wemail' ),
                'segments'          => __( 'Segments', 'wemail' ),
                'selectLists'       => __( 'Select Lists', 'wemal' ),
                'selectSegments'    => __( 'Select Segments', 'wemal' ),
                'setup'             => __( 'Setup', 'wemail' ),
                'template'          => __( 'Template', 'wemail' ),
                'design'            => __( 'Design', 'wemail' ),
                'send'              => __( 'Send', 'wemail' ),
                'next'              => __( 'Next', 'wemail' ),
                'previous'          => __( 'Previous', 'wemail' )
            ],
            'lists' => wemail()->api->get( '/lists/all' ),
            'segments' => wemail()->api->get( '/segments/all' ),
            'campaign' => $this->get_campaign( $params['id'] )
        ];
    }

    public function get_campaigns( $args ) {
        return wemail()->api->get( '/campaigns', $args );
    }

    public function get_campaign( $id ) {
        $campaign = wemail()->api->get( "/campaigns/{$id}" );

        if ( isset( $campaign['data'] ) ) {
            $campaign = $campaign['data'];
        } else {
            $campaign = null;
        }

        return $campaign;
    }

}
