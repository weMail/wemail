<?php

namespace WeDevs\WeMail\Core\Campaign;

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
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );
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
                'sent'                      => __( 'Sent', 'wemail' ),
                'opened'                    => __( 'Open', 'wemail' ),
                'clicked'                   => __( 'Click', 'wemail' ),
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
                'noCampaignFound'           => __( 'No campaign found', 'wemail' ),
                'searchCampaign'            => __( 'Search Campaigns', 'wemail' )
            ],
            'campaigns' => wemail()->campaign->all( $query ),
            'listTable' => [
                'columns' => [
                    'icon',
                    'name',
                    'status',
                    'lists',
                    'sent',
                    'opened',
                    'clicked',
                    'unsubscribed',
                    'createdAt'
                ],
                'sortableColumns' => [
                    'name'          => 'name',
                    'sent'          => 'sent',
                    'opened'        => 'opened',
                    'clicked'       => 'clicked',
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
        return wemail()->campaign->editor->get_setup_data();
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
            'campaign' => wemail()->campaign->get( $params['id'] ),
            'subscribers' => wemail()->api->campaigns( $params['id'] )->subscribers()->query( $query )->get()
        ];
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
        $data = wemail()->campaign->editor->get_setup_data();

        $campaign = wemail()->campaign->get( $params['id'], [ 'event', 'lists', 'segments', 'email' ] );

        $data['campaign']     = $campaign;
        $data['customizer']   = wemail()->campaign->editor->get_customizer_data();

        return $data;
    }

}
