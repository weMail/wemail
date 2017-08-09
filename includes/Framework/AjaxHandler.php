<?php
namespace WeDevs\WeMail\Framework\Traits;

trait AjaxHandler {

    protected function add_action( $tag, $nopriv = false ) {
        add_action( 'wp_ajax_wemail_' . $tag, [ $this, $tag ] );

        if ( $nopriv ) {
            add_action( 'wp_ajax_nopriv_wemail_' . $tag, [ $this, $tag ] );
        }
    }

    /**
     * Verify request nonce
     *
     * @since 1.0.0
     *
     * @param string the nonce action name
     *
     * @return void
     */
    public function verify_nonce( $action = 'wemail-nonce' ) {
        if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $action ) ) {
            $this->send_error( __( 'Error: Nonce verification failed', 'wemail' ) );
        }
    }

    /**
     * Wrapper function for sending success response
     *
     * @since 1.0.0
     *
     * @param mixed $data
     *
     * @return void
     */
    public function send_success( $data = null ) {
        wp_send_json_success( $data );
    }

    /**
     * Wrapper function for sending error
     *
     * @since 1.0.0
     *
     * @param mixed $data
     *
     * @return void
     */
    public function send_error( $data = null ) {
        wp_send_json_error( $data );
    }

}
