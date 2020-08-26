<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;
use WP_Error;

class ERP extends RestController {

    /**
     * Route base url
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $rest_base = '/erp';

    /**
     * Register routes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_routes() {
        $this->get('/crm/contact-groups', 'contact_groups', 'can_view_list');
        $this->get('/crm/contacts', 'contacts', 'can_view_subscriber');
        $this->get('/crm/contacts/(?P<id>[\d]+)', 'contact', 'can_view_subscriber');
    }

    /**
     * Get all CRM contact groups
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
    public function contact_groups( $request ) {
        $is_erp_crm_active = $this->is_erp_crm_active();

        if ( $is_erp_crm_active instanceof WP_Error ) {
            return $is_erp_crm_active;
        }

        $args = [
            'number'  => -1,
            'orderby' => 'created_at',
            'order'   => 'asc',
        ];

        $items = erp_crm_get_contact_groups( $args );

        $groups = [];

        foreach ( $items as $item ) {
            $groups[] = [
                'id'   => $item->id,
                'name' => $item->name
            ];
        }

        return rest_ensure_response( [ 'data' => $groups ] );
    }

    /**
     * Get CRM contacts
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
    public function contacts( $request ) {
        $is_erp_crm_active = $this->is_erp_crm_active();

        if ( $is_erp_crm_active instanceof WP_Error ) {
            return $is_erp_crm_active;
        }

        $include  = $request->get_param( 'include' );
        $after_id = $request->get_param( 'after_id' );

        $db     = \WeDevs\ORM\Eloquent\Facades\DB::instance();
        $prefix = $db->db->prefix;

        $contact_type = $db->table( 'erp_people_types' )->where( 'name', 'contact' )->first();

        if ( empty( $contact_type->id )  ) {
            return $this->respond_error( 'Contact type not found', 'contact_type_not_found' );
        }

        $type_id = $contact_type->id;

        $query = $db->table( 'erp_peoples as people' )
            ->select( 'people.*' )
            ->leftJoin( "{$prefix}erp_people_type_relations as rel", function ( $join ) {
                $join->on( 'people.id', '=', 'rel.people_id' );
            } )
            ->where( 'rel.people_types_id', $type_id )
            ->whereNull( 'rel.deleted_at' );

        if ( ! empty( $include ) ) {
            $query = $query->whereIn( 'people.id', $include );
        } else {
            $query = $query->take( 100 );
        }

        if ( ! empty( $after_id ) ) {
            $query = $query->where( 'people.id', '>', absint( $after_id ) );
        }

        $contacts = $query->orderBy( 'people.id', 'asc' )->get();

        $subscriptions = [];

        if ( $contacts->count() ) {
            $contact_ids = $contacts->pluck( 'id' );

            $subscriptions = $db->table( 'erp_crm_contact_subscriber' )
                ->select( 'user_id as contact_id', 'group_id', 'status', 'subscribe_at', 'unsubscribe_at' )
                ->whereIn( 'user_id', $contact_ids )
                ->orderBy( 'user_id', 'asc' )
                ->get()
                ->groupBy( 'contact_id' );

            $contacts->map( function ( $contact ) use ( $subscriptions ) {
                if ( isset( $subscriptions[$contact->id] ) ) {
                    $contact->groups = $subscriptions[$contact->id];
                } else {
                    $contact->groups = [];
                }

                return $contact;
            } );
        }

        return rest_ensure_response( [
            'data' => $contacts
        ] );
    }


    /**
     * Get a single contact
     *
     * @param  \WP_REST_Request $request
     *
     * @return WP_Error|\WP_REST_Response
     */
    public function contact($request) {
        $is_erp_crm_active = $this->is_erp_crm_active();

        if ( $is_erp_crm_active instanceof WP_Error ) {
            return $is_erp_crm_active;
        }

        $contact_id = $request->get_param( 'id' );

        /** @var \WeDevs\ERP\Framework\Models\People $contact */
        $contact = \WeDevs\ERP\Framework\Models\People::find( $contact_id );

        if ( is_null( $contact ) ) {
            return new \WP_REST_Response('Contact not found', self::HTTP_NOT_FOUND);
        }
        /** @var \WeDevs\ORM\Eloquent\Database $db */
        $db = \WeDevs\ORM\Eloquent\Facades\DB::instance();
        $prefix = $db->db->prefix;

        $groups = $db->table('erp_crm_contact_subscriber')
            ->selectRaw("contact_group.name, contact_group.description, {$prefix}erp_crm_contact_subscriber.*")
            ->join("{$prefix}erp_crm_contact_group as contact_group", "{$prefix}erp_crm_contact_subscriber.group_id", '=', "contact_group.id")
            ->where($prefix.'erp_crm_contact_subscriber.user_id', $contact->id)
            ->get()->toArray();

        $contact->groups = $groups;

        return rest_ensure_response( $contact );
    }

    /**
     * Checks if ERP CRM is enabled
     *
     * @since 1.0.0
     *
     * @return \WP_Error|bool
     */
    private function is_erp_crm_active() {
        if ( ! is_erp_crm_active() ) {
            return $this->respond_error( 'ERP CRM is not active', 'erp_crm_is_not_active' );
        }

        return true;
    }
}
