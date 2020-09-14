<?php
namespace WeDevs\WeMail\Core\Mail;

use PHPMailer;

class WeMailMailer54 extends PHPMailer {

    use MailerHelper;

    /**
     * Overwrite phpmailer send method
     *
     * @throws \phpmailerException
     */
    public function send() {
        $response = $this->attemptToSend();

        if ( is_wp_error( $response ) ) {
            throw new \phpmailerException( $response->get_error_message() );
        }

        if ( isset( $response['success'] ) && ! wemail_validate_boolean( $response['success'] ) ) {
            throw new \phpmailerException( 'Could not send transactional email' );
        }

        return true;
    }
}
