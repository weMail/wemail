<?php

namespace WeDevs\WeMail\Core\Sync\AffiliateWp;

use WeDevs\WeMail\Traits\Hooker;

class AffiliateWp {

    use Hooker;

    protected $source = 'affiliatewp';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        if ( is_admin() ) {
            add_action(
                'affwp_insert_affiliate',
                array( $this, 'affwp_wemail_add_user_to_list' ),
                10, 2
            );
            add_action(
                'affwp_update_affiliate_profile_settings',
                array( $this, 'affwp_wemail_affiliate_profile_updated' )
            );
            add_action( 'affwp_delete_affiliates', array( $this, 'affwp_wemail_remove_user_from_list' ), 10, 2 );

            add_action( 'affwp_update_affiliate', array( $this, 'affwp_wemail_affiliate_profile_updated' ), 10, 2 );
        } else {
            add_action(
                'affwp_register_user',
                array( $this, 'affwp_wemail_add_user_to_list' ),
                10, 2
            );
        }
    }

    /**
     * Sync affiliate
     *
     * @param $order_id
     * @return void
     * @since 1.0.0
     */
    public function affwp_wemail_add_user_to_list( $affiliate_id, $status = true ) {
        $is_enabled = get_option( 'wemail_affiliatewp_enabled', $default = false );
        if ( ! $is_enabled || $is_enabled === 'false' ) {
			return;
        }

        $post_data = $_POST; // phpcs:ignore
        $payload = [];
        if ( isset( $post_data['affwp_user_name'] ) ) {
            $name = explode( ' ', sanitize_text_field( $post_data['affwp_user_name'] ) );
            $first_name = $name[0];
            $last_name  = isset( $name[1] ) ? $name[1] : '';
            $email      = sanitize_text_field( $post_data['affwp_user_email'] );

            $affiliate = affiliate_wp()->affiliates->get_by( 'affiliate_id', $affiliate_id );
            $user_id   = $affiliate->user_id;

            $payload = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'wp_user_id' => $user_id,
            ];
        } elseif ( isset( $post_data['email'] ) ) {
            $payload = [
                'first_name' => $post_data['first_name'] ? $post_data['first_name'] : '',
                'last_name' => $post_data['last_name'] ? $post_data['last_name'] : '',
                'email' => $post_data['email'],
                'wp_user_id' => $post_data['wp_user_id'] ? $post_data['wp_user_id'] : '',
            ];
        } else {
            $affiliate = affwp_get_affiliate( $affiliate_id );
            $user = get_userdata( $affiliate->user_id );
            $name = explode( ' ', $user->display_name );
            $first_name = $name[0];
            $last_name  = isset( $name[1] ) ? $name[1] : '';
            $email      = $user->user_email;

            $payload = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'wp_user_id' => $user->ID,
            ];
        }
        wemail()->api->affiliates()->subscribers()->post( $payload );
    }

    public function affwp_wemail_remove_user_from_list( $data ) {
        error_log( 'Removing Affiliate' );
        error_log( $data );
    }

    public function affwp_wemail_affiliate_profile_updated( $data ) {
        error_log( 'Updating Affiliate Profile' );
        error_log( data );
    }
}
