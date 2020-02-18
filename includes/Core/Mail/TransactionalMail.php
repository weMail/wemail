<?php
namespace WeDevs\WeMail\Core\Mail;

class TransactionalMail {

    /* @var $phpmailer \PHPMailer*/
    protected $phpmailer;

    /**
     * TransactionalMail constructor.
     */
    public function __construct() {
        add_action( 'phpmailer_init', function ( &$phpmailer ) {
            if ( ! get_option( 'wemail_transactional_emails', false ) ) {
                return;
            }

            $this->phpmailer = clone $phpmailer;
            $phpmailer = $this;
        });
    }

    /**
     * Overwrite phpmailer send method
     *
     * @throws \phpmailerException
     */
    public function send() {
        $response = wemail()->api->emails()->transactional()->post( array(
            'to' => $this->formatEmailAddress($this->phpmailer->getToAddresses()),
            'subject' => $this->phpmailer->Subject,
            'message' => $this->phpmailer->Body,
            'type' => $this->phpmailer->ContentType,
            'attachments' => $this->phpmailer->getAttachments()
        ) );

        if ( isset( $response['success']) && ($response['success'] != 'true' || $response['success'] != 1 ) ) {
            throw new \phpmailerException( 'Could not send transactional email' );
        }

        return true;
    }

    /**
     *  Format Email Addresses
     *
     * @param $address
     * @return string
     */
    protected function formatEmailAddress( $address ) {
        $emailAddress = array_map(function ( $address ) {
            return $address[0];
        }, $address);

        return implode( ',', $emailAddress );
    }
}
