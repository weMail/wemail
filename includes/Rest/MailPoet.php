<?php

namespace WeDevs\WeMail\Rest;

use MailPoet\Models\Subscriber;
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
            array(
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => array( $this, 'permission' ),
                'callback' => array( $this, 'lists' ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/lists/(?P<id>[\d]+)/subscribers',
            array(
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => array( $this, 'permission' ),
                'callback' => array( $this, 'subscribers' ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/meta-fields',
            array(
                'methods' => WP_REST_Server::READABLE,
                'permission_callback' => array( $this, 'permission' ),
                'callback' => array( $this, 'meta_fields' ),
            )
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
                array(
                    'fields'        => 'ID',
                    'meta_key'      => 'wemail_api_key',
                    'meta_value'    => $api_key,
                )
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
        $callbacks = array( 'lists', 'meta_fields', 'subscribers' );

        if ( in_array( $name, $callbacks, true ) ) {
            $active_version = $this->active_version();

            if ( ! $active_version ) {
                return new WP_Error( 'mailpoet_is_not_active', __( 'MailPoet is not active', 'wemail' ), array( 'status' => 422 ) );
            }

            $method = ( $active_version === 'v3' ) ? "{$name}_v3" : "{$name}_v2";

            $request = $args[0];

            $data = array(
                $name => $this->$method( $request ),
            );

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
        global $wpdb;
        $segment_table = $wpdb->prefix . 'mailpoet_segments';
        $pivot_table = $wpdb->prefix . 'mailpoet_subscriber_segment';

        $lists = $wpdb->get_results( 'SELECT * FROM ' . $segment_table, ARRAY_A );
        $stats = $wpdb->get_results( $wpdb->prepare( 'SELECT mp_segments.id, COUNT(*) as count FROM ' . $pivot_table . ' as mp_segment_pivot INNER JOIN ' . $segment_table . ' as mp_segments ON mp_segment_pivot.segment_id = mp_segments.id AND mp_segment_pivot.status = %s GROUP BY mp_segments.id', Subscriber::STATUS_SUBSCRIBED ), ARRAY_A );

        $lists = array_column( $lists, 'name', 'id' );
        $stats = array_column( $stats, 'count', 'id' );

        return array_map(
            function ( $id ) use ( $lists, $stats ) {
                return array(
                    'id' => $id,
                    'name' => $lists[ $id ],
                    'count' => intval( $stats[ $id ] ),
                );
            },
            array_keys( $stats )
        );
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

        $data = array();

        foreach ( $lists as $list ) {
            $list_id = absint( $list['list_id'] );
            $count = absint( $users->countSubscribers( array( $list_id ) ) );

            $data[] = array(
                'id' => $list_id,
                'name' => $list['name'],
                'count' => $count,
            );
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
        $data = array();

        $fields = \MailPoet\API\API::MP( 'v1' )->getSubscriberFields();

        foreach ( $fields as $field ) {
            $data[] = array(
                'name' => $field['id'],
                'title' => $field['name'],
            );
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
        $data = array();

        $user = \WYSIJA::get( 'user_field', 'model' );

        $fields = $user->getFields();

        foreach ( $fields as $name => $title ) {
            $data[] = array(
                'name' => $name,
                'title' => $title,
            );
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
        global $wpdb;

        $list_id = absint( $request['id'] );
        $offset = ( ! empty( $request['offset'] ) ? $request['offset'] : 0 );
        $limit = ( ! empty( $request['limit'] ) ? $request['limit'] : 20 );

        return $wpdb->get_results( $wpdb->prepare( "SELECT subscribers.id, subscribers.first_name, subscribers.last_name, subscribers.email FROM {$wpdb->prefix}mailpoet_subscriber_segment as sub_segment INNER JOIN {$wpdb->prefix}mailpoet_subscribers as subscribers ON sub_segment.subscriber_id = subscribers.id AND sub_segment.status = %s WHERE sub_segment.segment_id = %d LIMIT %d, %d", Subscriber::STATUS_SUBSCRIBED, $list_id, $offset, $limit ), ARRAY_A );
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
            return new WP_Error( 'required_field', __( 'List id is missing', 'wemail' ), array( 'status' => 422 ) );
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

        $filters = array();

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
