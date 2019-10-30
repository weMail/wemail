<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;

class Users extends RestController
{

    public $rest_base = '/static/users';

    public function __construct()
    {
        $this->register_routes();
    }

    public function register_routes()
    {
        $this->get('/', 'index', 'permissions');
        $this->get('/roles', 'roles', 'permissions');
        $this->post('/roles', 'storeRoles', 'permissions');
        $this->post('/', 'store', 'permissions');
        $this->post('/status', 'toggleStatus', 'permissions');
        $this->post('/move', 'update', 'permissions');
    }

    public function permissions( $request ) {
        return wemail()->user->can( 'manage_settings' );
    }

    public function index()
    {
        $roles = get_option( 'wemail_accessible_roles', $default = false );
        $args = array(
            'role__in' => $roles ? $roles : ['administrator', 'editor'],
            'exclude' => [get_current_user_id()]
        );

        return rest_ensure_response([
            'data' => array_map(function ($user) {
                $map = get_user_meta($user->ID, 'wemail_user_data', null);
                $hash = isset($map[0]) && isset($map[0]['hash']) ? $map[0]['hash'] : null;
                return [
                    'id'       => $user->ID,
                    'name'     => $user->display_name,
                    'email'    => $user->user_email,
                    'roles'    => $user->roles,
                    'hash'     => $hash
                ];
            }, get_users($args))
        ]);
    }

    public function store($request)
    {
        $users = $request->get_param('users');
        $role = $request->get_param('role');

        if ($access_token = get_user_meta(get_current_user_id(), 'wemail_api_key', false)) {
            wemail()->api->set_api_key($access_token[0]);

            foreach ($users as $user) {
                $wp_admin_response = wemail()->api->auth()->users()->post([
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $role
                ]);

                if (isset($wp_admin_response['access_token']) && $wp_admin_response['access_token'] !== '') {
                    update_user_meta($user['id'], 'wemail_api_key', $wp_admin_response['access_token']);
                }
            }

            return $this->respond(null, self::HTTP_CREATED);
        }

        return $this->respond(['message' => 'Access token not found'], 422);
    }

    public function update($request)
    {
        $users = $request->get_param('users');
        $role = $request->get_param('role');

        if ($access_token = get_user_meta(get_current_user_id(), 'wemail_api_key', false)) {
            wemail()->api->set_api_key($access_token[0]);
            foreach ($users as $user) {
                $wp_admin_response = wemail()->api->auth()->users()->move()->post([
                    'email' => $user,
                    'role'  => $role
                ]);

                $wpUser = get_user_by( 'email', $user );
                if (isset($wpUser->ID)) {
                    delete_user_meta($wpUser->ID, 'wemail_user_data');
                }
            }

            return $this->respond(null, self::HTTP_CREATED);
        }

        return $this->respond(['message' => 'Access token not found'], 422);
    }

    public function toggleStatus($request)
    {
        $email = $request->get_param('email');
        $status = $request->get_param('status');
        $user = get_user_by( 'email', $email );
        if ($user) {
            $wp_admin_response = wemail()->api->auth()->users()->status()->post([
                'email'  => $email,
                'status' => $status
            ]);
            if ($wp_admin_response['success']) {
                if (isset($wp_admin_response['token'])) {
                    update_user_meta( $user->ID, 'wemail_api_key', $wp_admin_response['token'] );
                }

                if ($status === 'disable') {
                    update_user_meta( $user->ID, 'wemail_disable_user_access', true );
                } else {
                    delete_user_meta( $user->ID, 'wemail_disable_user_access' );
                }

                return $this->respond(['message' => 'User status changed'], 200);
            }

            return $this->respond(['message' => $wp_admin_response], 422);
        }

        return $this->respond(['message' => "User was not found in your site.It maybe removed previously."], 422);
    }

    public function roles()
    {
        $accessibleRoles = get_option( 'wemail_accessible_roles', $default = false );
        $roles = wp_roles();
        $availableRoles = [];
        foreach ($roles->roles as $key => $role) {
            if ($key !== 'subscriber') {
                array_push($availableRoles, [
                    'slug' => $key,
                    'name' => $role['name']
                ]);
            }
        }

        return $this->respond([
            'data' => [
                'roles' => $availableRoles,
                'accessible_roles' => $accessibleRoles ? $accessibleRoles : ['administrator', 'editor']
            ]
        ], 200);
    }

    public function storeRoles($request)
    {
        $roles = $request->get_param('roles');

        update_option( 'wemail_accessible_roles', $roles );

        return $this->respond([
            'success' => true
        ], 200);
    }
}
