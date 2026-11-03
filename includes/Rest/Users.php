<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;

class Users extends RestController {

    public $rest_base = '/static/users';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        $this->get( '/', 'index', 'permissions' );
        $this->get( '/roles', 'roles', 'permissions' );
        $this->post( '/roles', 'storeRoles', 'permissions' );
        $this->post( '/', 'store', 'permissions' );
        $this->post( '/status', 'toggleStatus', 'permissions' );
        $this->post( '/move', 'update', 'permissions' );
    }

    public function permissions( $request ) {
        return current_user_can( 'manage_options' );
    }

    public function index() {
        $roles = get_option( 'wemail_accessible_roles', $default = false );
        $args = array(
            'role__in' => $roles ? $roles : array( 'administrator', 'editor' ),
            'exclude' => array( get_current_user_id() ),
        );

        return rest_ensure_response(
            array(
                'data' => array_map(
                    function ( $user ) {
                        $map = get_user_meta( $user->ID, 'wemail_user_data', null );
                        $hash = isset( $map[0] ) && isset( $map[0]['hash'] ) ? $map[0]['hash'] : null;
                        return array(
                            'id'       => $user->ID,
                            'name'     => $user->display_name,
                            'email'    => $user->user_email,
                            'roles'    => $user->roles,
                            'hash'     => $hash,
                        );
                    },
                    get_users( $args )
                ),
            )
        );
    }

    /**
     * Save the weMail accessible roles
     *
     * @since 2.1.7 The local option is written only after the cloud push succeeds,
     * so a failure response can no longer leave new roles saved.
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed
     */
    public function storeRoles( $request ) {
        $roles = $this->validateAccessibleRoles( $request );

        if ( false === $roles ) {
            return $this->respond( array( 'message' => 'Invalid roles provided' ), 422 );
        }

        $access_token = get_option( 'wemail_api_key' );

        if ( empty( $access_token ) ) {
            return $this->respond( array( 'message' => 'API token not available' ), 422 );
        }

        $response = wemail()->api->wp()->users()->rolePermissions()->post(
            array(
                'roles'     => $roles,
                'api_token' => $access_token,
            )
        );

        if ( is_wp_error( $response ) || ! isset( $response['success'] ) || true !== $response['success'] ) {
            // Nothing was written locally, so the saved state and this response agree.
            return $this->respond( array( 'message' => 'Failed to update roles' ), 422 );
        }

        update_option( 'wemail_accessible_roles', $roles );

        return $this->respond( array( 'message' => $response['message'] ), 200 );
    }

    public function update( $request ) {
        $users = $request->get_param( 'users' );
        $role = $request->get_param( 'role' );

        $access_token = get_user_meta( get_current_user_id(), 'wemail_api_key', false );
        if ( $access_token ) {
            wemail()->api->set_api_key( $access_token[0] );
            foreach ( $users as $user ) {
                $response = wemail()->api->teamUsers()->update()->put(
                    array(
                        'email'   => $user,
                        'role'    => $role,
                        'include' => 'role,permissions',
                    )
                );

                // Cloud failure returns WP_Error; skip caching meta for this user.
                if ( is_wp_error( $response ) || empty( $response['data'] ) ) {
                    continue;
                }

                $wp_user = get_user_by( 'email', $user );
                if ( isset( $wp_user->ID ) ) {
                    $user_meta = array(
                        'deleted_at'  => $response['data']['deleted_at'],
                        'email'       => $response['data']['email'],
                        'hash'        => $response['data']['hash'],
                        'name'        => $response['data']['name'],
                        'permissions' => $response['data']['permissions'],
                        'role'        => $response['data']['role'],
                        'roles'       => $response['data']['roles'],
                    );
                    update_user_meta( $wp_user->ID, 'wemail_user_data', $user_meta );
                }
            }

            return $this->respond( array( 'success' => true ), self::HTTP_CREATED );
        }

        return $this->respond( array( 'message' => 'Access token not found' ), 422 );
    }

    public function toggleStatus( $request ) {
        $email = $request->get_param( 'email' );
        $status = $request->get_param( 'status' );
        $user = get_user_by( 'email', $email );
        if ( $user ) {
            $wp_admin_response = wemail()->api->team()->users()->status()->post(
                array(
                    'email'  => $email,
                    'status' => $status,
                )
            );

            if ( is_wp_error( $wp_admin_response ) ) {
                return $this->respond( array( 'message' => 'Failed to update user status' ), 422 );
            }

            if ( ! empty( $wp_admin_response['success'] ) ) {
                if ( isset( $wp_admin_response['token'] ) ) {
                    update_user_meta( $user->ID, 'wemail_api_key', $wp_admin_response['token'] );
                }

                if ( $status === 'disable' ) {
                    update_user_meta( $user->ID, 'wemail_disable_user_access', true );
                } else {
                    delete_user_meta( $user->ID, 'wemail_disable_user_access' );
                }

                return $this->respond( array( 'message' => 'User status changed' ), 200 );
            }

            return $this->respond( array( 'message' => $wp_admin_response ), 422 );
        }

        return $this->respond(
            array(
                'message' => 'User was not found in your site.It maybe removed previously.',
            ),
            422
        );
    }

    public function roles() {
        $accessible_roles = get_option( 'wemail_accessible_roles', $default = false );
        $roles = wp_roles();
        $available_roles = array();
        foreach ( $roles->roles as $key => $role ) {
            if ( $key !== 'subscriber' ) {
                $available_roles[] = array(
                    'slug' => $key,
                    'name' => $role['name'],
                );
            }
        }

        return $this->respond(
            array(
                'data' => array(
                    'roles' => $available_roles,
                    'accessible_roles' => $accessible_roles ? $accessible_roles : array( 'administrator', 'editor' ),
                ),
            ),
            200
        );
    }

    public function store( $request ) {
        $args = array(
            'role' => $request->get_param( 'role' ),
            'exclude' => array( get_current_user_id() ),
        );

        $users = get_users( $args );

        foreach ( $users as $user ) {
            delete_user_meta( $user->ID, 'wemail_user_data' );
        }

        return $this->respond( array( 'success' => true ), 200 );
    }

    /**
     * Validate the weMail accessible roles without writing anything
     *
     * @since 2.1.7 Validates and returns the sanitized roles; the caller persists
     * them only after the cloud push succeeds. Returns false on invalid input.
     *
     * @param \WP_REST_Request $request
     *
     * @return array|false
     */
    public function validateAccessibleRoles( $request ) {
        $roles = $request->get_param( 'roles' );

        if ( empty( $roles ) || ! is_array( $roles ) ) {
            return false;
        }

        $registered = array_keys( wp_roles()->roles );
        $sanitized  = array();

        foreach ( $roles as $role ) {
            // Verbatim check: accept registered slugs exactly as registered —
            // no key normalization that could reject a legitimate slug.
            if ( ! is_string( $role ) || ! in_array( $role, $registered, true ) ) {
                return false;
            }

            $sanitized[] = $role;
        }

        return array_values( array_unique( $sanitized ) );
    }
}
