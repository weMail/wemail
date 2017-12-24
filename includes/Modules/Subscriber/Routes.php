<?php

namespace WeDevs\WeMail\Modules\Subscriber;

use WeDevs\WeMail\Framework\Traits\Hooker;

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
        $this->add_filter( 'wemail_get_route_data_subscriberIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_subscriberEdit', 'edit', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_subscriberShow', 'show', 10, 2 );
    }

    /**
     * Subscribers list table data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function index( $params, $query ) {
        if ( !empty( $params['lifeStage'] ) ) {
            $query['life_stage'] = $params['lifeStage'];
        }

        if ( !empty( $params['status'] ) ) {
            $query['status'] = $params['status'];
        }

        $life_stages = wemail()->settings->life_stages();

        $i18n = [
            'subscribers'            => __( 'Subscribers', 'wemail' ),
            'addNew'                 => __( 'Add New', 'wemail' ),
            'searchSegment'          => __( 'Search Segment', 'wemail' ),
            'addNewSubscriber'       => __( 'Add New Subscriber', 'wemail' ),
            'cancel'                 => __( 'Cancel', 'wemail' ),
            'save'                   => __( 'Save', 'wemail' ),
            'saveAndAddAnother'      => __( 'Save and add another', 'wemail' ),
            'saveAndGoToDetailsPage' => __( 'Save and go to details page', 'wemail' ),
            'email'                  => __( 'Email', 'wemail' ),
            'firstName'              => __( 'First Name', 'wemail' ),
            'lastName'               => __( 'Last Name', 'wemail' ),
            'phone'                  => __( 'Phone', 'wemail' ),
            'requiredField'          => __( 'required field', 'wemail' ),
            'invalidEmail'           => __( 'invalid email', 'wemail' ),
            'lists'                  => __( 'Lists', 'wemail' ),
            'noName'                 => __( 'no name', 'wemail' ),
            'delete'                 => __( 'Delete', 'wemail' ),
            'cancel'                 => __( 'Cancel', 'wemail' ),
            'close'                  => __( 'Close', 'wemail' ),
            'subscriberDeleted'      => __( 'Subscriber deleted', 'wemail' ),
            'deleteSubWarnMsg'       => __( 'Are you sure you want to delete this subscriber? This subscriber will be removed from all lists and your action cannot be undone.', 'wemail' ),
            'deleteSubsWarnMsg'      => __( 'Are you sure you want to delete these subscribers? These subscribers will be removed from all lists and your action cannot be undone.', 'wemail' ),
            'name'                   => __( 'Name', 'wemail' ),
            'emailAddress'           => __( 'Email Address', 'wemail' ),
            'lifeStage'              => __( 'Life Stage', 'wemail' ),
            'createdAt'              => __( 'Created At', 'wemail' ),
            'edit'                   => __( 'Edit', 'wemail' ),
            'view'                   => __( 'View', 'wemail' ),
            'searchSubscribers'      => __( 'Search Subscribers', 'wemail' ),
            'bulkActions'            => __( 'Bulk Actions', 'wemail' ),
            'moveToTrash'            => __( 'Move to Trash', 'wemail' ),
            'noDataFound'            => __( 'No data found', 'wemail' ),
            'noSubscriberFound'      => __( 'No subscriber found', 'wemail' ),
            'quickEdit'              => __( 'Quick Edit', 'wemail' ),
            'trash'                  => __( 'Trash', 'wemail' ),
            'apply'                  => __( 'Apply', 'wemail' ),
            'deletePermanently'      => __( 'Delete Permanently', 'wemail' ),
            'restore'                => __( 'Restore', 'wemail' ),
            'all'                    => __( 'All', 'wemail' ),
            'trashed'                => __( 'Trashed', 'wemail' ),
            'items'                  => __( 'items', 'wemail' ),
        ];

        return [
            'i18n' => array_merge( $i18n, $life_stages['i18n'] ),
            'subscribers'   => wemail()->subscriber->all( $query ),
            'lists'         => wemail()->lists->items(),
            'listTable'     => [
                'columns' => [
                    'name',
                    'emailAddress',
                    'phone',
                    'lifeStage',
                    'createdAt'
                ],
                'sortableColumns' => [
                    'name' => 'first_name',
                    'emailAddress' => 'email',
                    'lifeStage' => 'life_stage',
                    'createdAt' => 'created_at'
                ]
            ]
        ];
    }

    /**
     * Subscribers list table data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function edit( $params, $query ) {
        return [
            'subscriber' => wemail()->subscriber->get( $params['id'] )
        ];
    }

    /**
     * Single subscriber page route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function show( $params, $query ) {
        $i18n = [
            'noName'             => __( 'no name', 'wemail' ),
            'website'            => __( 'Website', 'wemail' ),
            'informations'       => __( 'Informations', 'wemail' ),
            'lists'              => __( 'Lists', 'wemail' ),
            'subscriberNotFound' => __( 'Subscriber not found', 'wemail' ),
            'dob'                => __( 'Date of birth', 'wemail' ),
            'address1'           => __( 'Address 1', 'wemail' ),
            'address2'           => __( 'Address 2', 'wemail' ),
            'city'               => __( 'City', 'wemail' ),
            'state'              => __( 'State', 'wemail' ),
            'country'            => __( 'Country', 'wemail' ),
            'zip'                => __( 'Zip', 'wemail' ),
            'editInfo'           => __( 'edit info', 'wemail' ),
            'save'               => __( 'Save', 'wemail' ),
            'cancel'             => __( 'Cancel', 'wemail' ),
            'unconfirmed'        => __( 'unconfirmed', 'wemail' ),
            'unsubscribed'       => __( 'unsubscribed', 'wemail' ),
            'subscribed'         => __( 'subscribed', 'wemail' ),
            'deleteSubscriber'   => __( 'Delete Subscriber', 'wemail' ),
            'delete'             => __( 'Delete', 'wemail' ),
            'cancel'             => __( 'Cancel', 'wemail' ),
            'close'              => __( 'Close', 'wemail' ),
            'subscriberDeleted'  => __( 'Subscriber deleted', 'wemail' ),
            'deleteSubWarnMsg'   => __( 'Are you sure you want to delete this subscriber? This subscriber will be removed from all lists and your action cannot be undone.', 'wemail' ),
            'edit'               => __( 'Edit', 'wemail' ),
            'remove'             => __( 'Remove', 'wemail' ),
            'firstName'          => __( 'First Name', 'wemail' ),
            'lastName'           => __( 'Last Name', 'wemail' ),
        ];

        $social_networks = wemail()->settings->social_networks->networks();
        array_unshift($social_networks, 'website');

        $social_network_icons = wemail()->settings->social_networks->icons();
        $social_network_icons = array_merge( $social_network_icons, [
            'website' => '<i class="fa fa-globe"></i>'
        ] );

        return [
            'i18n'              => $i18n,
            'subscriber'        => wemail()->subscriber->get( $params['id'] ),
            'lists'             => wemail()->lists->items(),
            'socialNetworks'    => [
                'networks'  => $social_networks,
                'icons'     => $social_network_icons
            ]
        ];
    }

}
