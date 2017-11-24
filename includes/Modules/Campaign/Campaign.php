<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;
use WeDevs\WeMail\Modules\Campaign\Event;
use WeDevs\WeMail\Modules\Campaign\Editor;

class Campaign extends Module {

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    private $menu_priority = 2;

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
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );

        $this->event  = new Event();
        $this->editor = new Editor();
    }

    /**
     * Register submenu
     *
     * @since 1.0.0
     *
     * @param array $menu_items
     * @param string $capability
     *
     * @return array
     */
    public function register_submenu( $menu_items, $capability ) {
        if ( wemail()->user->can( 'view_campaign' ) ) {
            $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];
        }

        return $menu_items;
    }

    /**
     * Campaign list table data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function index( $params, $query ) {
        if ( !empty( $params['type'] ) ) {
            $query['type'] = $params['type'];
        }

        if ( !empty( $params['status'] ) ) {
            $query['status'] = $params['status'];
        }

        return [
            'i18n' => [
                'campaigns'                 => __( 'Campaigns', 'wemail' ),
                'addNew'                    => __( 'Add New', 'wemail' ),
                'all'                       => __( 'All', 'wemail' ),
                'automatic'                 => __( 'Automatic', 'wemail' ),
                'standard'                  => __( 'Standard', 'wemail' ),
                'active'                    => __( 'Active', 'wemail' ),
                'paused'                    => __( 'Paused', 'wemail' ),
                'draft'                     => __( 'Draft', 'wemail' ),
                'sent'                      => __( 'Sent', 'wemail' ),
                'trashed'                   => __( 'Trashed', 'wemail' ),
                'name'                      => __( 'Name', 'wemail' ),
                'status'                    => __( 'Status', 'wemail' ),
                'lists'                     => __( 'Lists', 'wemail' ),
                'opened'                    => __( 'Open', 'wemail' ),
                'clicked'                   => __( 'Click', 'wemail' ),
                'bounced'                   => __( 'Bounce', 'wemail' ),
                'unsubscribed'              => __( 'Unsub', 'wemail' ),
                'createdAt'                 => __( 'Created At', 'wemail' ),
                'moveToTrash'               => __( 'Move to Trash', 'wemail' ),
                'restore'                   => __( 'Restore', 'wemail' ),
                'deletePermanently'         => __( 'Delete Permanently', 'wemail' ),
                'bulkActions'               => __( 'Bulk Actions', 'wemail' ),
                'apply'                     => __( 'Apply', 'wemail' ),
                'edit'                      => __( 'Edit', 'wemail' ),
                'trash'                     => __( 'Trash', 'wemail' ),
                'view'                      => __( 'View', 'wemail' ),
                'items'                     => __( 'items', 'wemail' ),
                'deleteCampaignsWarnMsg'    => __( 'Are you sure you want to delete these campaigns?', 'wemail' ),
                'deleteCampaignWarnMsg'     => __( 'Are you sure you want to delete this campaign?', 'wemail' ),
                'delete'                    => __( 'Delete', 'wemail' ),
                'cancel'                    => __( 'Cancel', 'wemail' ),
                'campaignDeleted'           => __( 'Campaign deleted', 'wemail' ),
                'close'                     => __( 'Close', 'wemail' ),
            ],
            'campaigns' => $this->all( $query ),
            'listTable' => [
                'columns' => [
                    'name',
                    'status',
                    'lists',
                    'opened',
                    'clicked',
                    'bounced',
                    'unsubscribed',
                    'createdAt'
                ],
                'sortableColumns' => [
                    'name'          => 'name',
                    'opened'        => 'opened',
                    'clicked'       => 'clicked',
                    'bounced'       => 'bounced',
                    'unsubscribed'  => 'unsubscribed',
                    'createdAt'     => 'created_at'
                ]
            ]
        ];
    }

    /**
     * Campaign create route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function create( $params, $query ) {
        return $this->editor->get_setup_data();
    }

    /**
     * Campaign single page route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function show( $params, $query ) {
        return [
            'i18n' => [
                'campaign' => __( 'List', 'wemail' )
            ],
            'campaign' => $this->get( $params['id'] )
        ];
    }

    // this will remove after finishing the templating system
    public function testTemplate( $campaign ) {

        $campaign['email']['template'] = wemail()->api->templates('basic')->get();

        return $campaign;
    }

    /**
     * Campaign editing page route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function edit( $params, $query ) {
        $data = $this->editor->get_setup_data();

        $campaign = $this->testTemplate( $this->get( $params['id'] ) );

        $data['campaign']     = $campaign;
        $data['customizer']   = $this->editor->get_customizer_data();

        return $data;
    }

    public function all( $query = [] ) {
        return wemail()->api->campaigns()->query( $query )->get();
    }

    /**
     * Get campaign data
     *
     * @since 1.0.0
     *
     * @param integer $id
     *
     * @return array
     */
    public function get( $id ) {
        $campaign = wemail()->api->campaigns( $id )->get();

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
