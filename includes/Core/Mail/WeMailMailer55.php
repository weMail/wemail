<?php
namespace WeDevs\WeMail\Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class WeMailMailer55 extends PHPMailer {

    use MailerHelper;

    /**
     * Overwrite phpmailer send method
     *
     * @throws PHPMailerException
     */
    public function send() {
        $response = $this->attemptToSend();

        if ( is_wp_error( $response ) ) {
            throw new PHPMailerException( esc_html( $response->get_error_message() ) );
        }

        if ( isset( $response['success'] ) && ! wemail_validate_boolean( $response['success'] ) ) {
            throw new PHPMailerException( 'Could not send transactional email' );
        }

        return true;
    }
}
