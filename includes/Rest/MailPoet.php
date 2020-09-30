<?php

namespace WeDevs\WeMail\Rest;

use WP_Error;
use WP_REST_Response;
use WP_REST_Server;
use WP_User_Query;

class MailPoet {

    /**
     * API Namespace
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $namespace = 'wemail/v1';

    /**
     * REST API Base
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $rest_base = '/mailpoet';

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->register_routes();
    }

    /**
     * Register route endpoints
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->rest_base . '/lists',
            [
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback' => [ $this, 'lists' ],
            ]
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/lists/(?P<id>[\d]+)/subscribers',
            [
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback' => [ $this, 'subscribers' ],
            ]
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/meta-fields',
            [
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback' => [ $this, 'meta_fields' ],
            ]
        );
    }

    /**
     * API Permission callback
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $requests
     *
     * @return bool
     */
    public function permission( $request ) {
        $api_key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $api_key ) ) {
            $query = new WP_User_Query(
                [
                    'fields'        => 'ID',
                    'meta_key'      => 'wemail_api_key',
                    'meta_value'    => $api_key,
                ]
            );

            if ( $query->get_total() ) {
                $results = $query->get_results();
                $user_id = array_pop( $results );

                wp_set_current_user( $user_id );

                return wemail()->user->can( 'create_subscriber' );
            }
        }

        return false;
    }

    /**
     * Magic method to call version dependent callbacks dynamically
     *
     * @since 1.0.0
     *
     * @param string $name
     * @param array $args
     *
     * @return \WP_Error|\WP_REST_Response|void
     */
    public function __call( $name, $args ) {
        $callbacks = [ 'lists', 'meta_fields', 'subscribers' ];

        if ( in_array( $name, $callbacks, true ) ) {
            $active_version = $this->active_version();

            if ( ! $active_version ) {
                return new WP_Error( 'mailpoet_is_not_active', __( 'MailPoet is not active', 'wemail' ), [ 'status' => 422 ] );
            }

            $method = ( $active_version === 'v3' ) ? "{$name}_v3" : "{$name}_v2";

            $request = $args[0];

            $data = [
                $name => $this->$method( $request ),
            ];

            return new WP_REST_Response( $data, 200 );
        }
    }

    /**
     * Active MailPoet version checker
     *
     * @since 1.0.0
     *
     * @return string|null
     */
    private function active_version() {
        if ( class_exists( 'MailPoet\Listing\Handler' ) ) {
            return 'v3';
        }

        if ( class_exists( 'WYSIJA' ) ) {
            return 'v2';
        }

        return null;
    }

    /**
     * MailPoet v3 lists
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function lists_v3() {
        $lists = new \MailPoet\Listing\Handler();
        $lists = $lists->get( '\MailPoet\Models\Segment', [] );

        $data = [];

        foreach ( $lists['items'] as $list ) {
            $list = $list->withSubscribersCount();

            $data[] = [
                'id' => absint( $list->id ),
                'name' => $list->name,
                'count' => $list->subscribers_count['subscribed'],
            ];
        }

        return $data;
    }

    /**
     * MailPoet v2 lists
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function lists_v2() {
        $mailpoet_form_engine = \WYSIJA::get( 'form_engine', 'helper' );

        $lists = $mailpoet_form_engine->get_lists();

        $users = \WYSIJA::get( 'user', 'model' );

        $data = [];

        foreach ( $lists as $list ) {
            $list_id = absint( $list['list_id'] );
            $count = absint( $users->countSubscribers( [ $list_id ] ) );

            $data[] = [
                'id' => $list_id,
                'name' => $list['name'],
                'count' => $count,
            ];
        }

        return $data;
    }

    /**
     * MailPoet v3 meta fields
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function meta_fields_v3() {
        $data = [];

        $fields = \MailPoet\API\API::MP( 'v1' )->getSubscriberFields();

        foreach ( $fields as $field ) {
            $data[] = [
                'name' => $field['id'],
                'title' => $field['name'],
            ];
        }

        return $data;
    }

    /**
     * MailPoet v2 meta fields
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function meta_fields_v2() {
        $data = [];

        $user = \WYSIJA::get( 'user_field', 'model' );

        $fields = $user->getFields();

        foreach ( $fields as $name => $title ) {
            $data[] = [
                'name' => $name,
                'title' => $title,
            ];
        }

        return $data;
    }

    /**
     * MailPoet v3 subscribers in a list
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function subscribers_v3( $request ) {
        $data = [];

        $list_id = absint( $request['id'] );

        $args = [
            'offset' => ! empty( $request['offset'] ) ? $request['offset'] : 0,
            'limit' => ! empty( $request['limit'] ) ? $request['limit'] : 20,
            'filter' => [
                'segment' => $list_id,
            ],
        ];

        /**
         * NOTE: Ignoring custom fields.
         */
        $listings = new \MailPoet\Segments\SubscribersListings(
            new \MailPoet\Listing\Handler(),
            new \MailPoet\WP\Functions()
        );
        $listing_data = $listings->getListingsInSegment( $args );

        foreach ( $listing_data['items'] as $subscriber ) {
            $data[] = $subscriber->asArray();
        }

        return $data;
    }

    /**
     * MailPoet v2 subscribers in a list
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function subscribers_v2( $request ) {
        if ( empty( $request['id'] ) ) {
            return new WP_Error( 'required_field', __( 'List id is missing', 'wemail' ), [ 'status' => 422 ] );
        }

        // get subscribers
        $import_confirmed_only = isset( $request['import_confirmed_only'] ) ? wemail_validate_boolean( $request['import_confirmed_only'] ) : false;

        $users = \WYSIJA::get( 'user', 'model' );

        $limit = ! empty( $request['limit'] ) ? $request['limit'] : 10;
        $users->limit_pp = $limit;

        // these two are required variables to get subscribers using MailPoet `WYSIJA::get( 'user', 'model' )->get_subscribers( $select, $filters )`
        if ( ! empty( $request['offset'] ) ) {
            $page = ceil( $request['offset'] / $limit ) + 1;
        } else {
            $page = 1;
        }

        $_REQUEST['pagi'] = $page;
        $_REQUEST['page'] = 'wysija_subscribers';

        $select = array( 'A.*, B.sub_date, B.unsub_date' );

        $filters = [];

        $filters['lists'] = $request['id'];

        /**
         * @Todo: Implement this feature
         *
         * if ( $import_confirmed_only ) {
         *     $filters['status'] = 'subscribed';
         * }
         */

        return $users->get_subscribers( $select, $filters );
    }
}
