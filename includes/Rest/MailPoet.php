<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Response;
use WP_REST_Server;
use WP_User_Query;

class MailPoet {

    public $namespace = 'wemail/v1';

    public $rest_base = '/mailpoet/v2';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, $this->rest_base . '/lists', [
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'permission' ],
            'callback' => [ $this, 'lists' ]
        ] );

        register_rest_route( $this->namespace, $this->rest_base . '/lists/(?P<id>[\d]+)/subscribers', [
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'permission' ],
            'callback' => [ $this, 'subscribers' ]
        ] );

        register_rest_route( $this->namespace, $this->rest_base . '/meta-fields', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'meta_fields' ],
        ] );
    }

    public function permission( $request ) {
        $api_key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $api_key ) ) {
            $query = new WP_User_Query( [
                'fields'        => 'ID',
                'meta_key'      => 'wemail_api_key',
                'meta_value'    => $api_key
            ] );

            if ( $query->get_total() ) {
                $results = $query->get_results();
                $user_id = array_pop( $results );

                wp_set_current_user( $user_id );

                return wemail()->user->can( 'create_subscriber' );
            }
        }

        return false;
    }

    public function lists() {
        $mailpoet_form_engine = \WYSIJA::get('form_engine', 'helper');

        $lists = $mailpoet_form_engine->get_lists();

        $users = \WYSIJA::get( 'user', 'model' );

        $data = [
            'lists' => []
        ];

        foreach ( $lists as $list ) {
            $list_id = absint( $list['list_id'] );
            $count = absint( $users->countSubscribers( [ $list_id ] ) );

            $data['lists'][] = [
                'id' => $list_id,
                'name' => $list['name'],
                'type' => wemail_validate_boolean( $list['is_public'] ) ? 'public' : 'private',
                'count' => $count
            ];
        }

        return new WP_REST_Response( $data, 200 );
    }

    public function meta_fields() {
        $data = [
            'fields' => []
        ];

        $user = \WYSIJA::get( 'user_field', 'model' );

        $fields = $user->getFields();

        foreach ( $fields as $name => $title ) {
            $data['fields'][] = [
                'name' => $name,
                'title' => $title
            ];
        }

        return new WP_REST_Response( $data, 200 );
    }

    public function subscribers( $request ) {
        $data = [
            'subscribers' => []
        ];

        if ( empty( $request['id'] ) ) {
            return new WP_Error( 'required_field', __( 'List id is missing', 'wemail' ), [ 'status' => 422 ] );
        }

        // get subscribers
        $import_confirmed_only = isset( $request['import_confirmed_only'] ) ? wemail_validate_boolean( $request['import_confirmed_only'] ) : false;

        $users = \WYSIJA::get( 'user', 'model' );

        $limit = ! empty( $request['limit'] ) ? $request['limit'] : 10;
        $users->limit_pp = $limit;

        // these two are required variables to get subscribers using Mailpoet `WYSIJA::get( 'user', 'model' )->get_subscribers( $select, $filters )`
        if (! empty( $request['offset'] ) ) {
            $page = ceil( $request['offset'] / $limit ) + 1;
        } else {
            $page = 1;
        }

        $_REQUEST['pagi'] = $page;
        $_REQUEST['page'] = 'wysija_subscribers';

        $select = array( 'A.*, B.sub_date, B.unsub_date' );

        $filters = [];

        $filters['lists'] = $request['id'];

        // @todo: Implement this feature
        // if ( $import_confirmed_only ) {
        //     $filters['status'] = 'subscribed';
        // }

        $data['subscribers'] = $users->get_subscribers( $select, $filters );

        return new WP_REST_Response( $data, 200 );
    }

}
