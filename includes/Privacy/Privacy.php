<?php

namespace WeDevs\WeMail\Privacy;

class Privacy {

    /**
     * Privacy object name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $name;

    /**
     * The list of exporters
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $exporters = array();

    /**
     * List of eraser
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $eraser = array();

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->name = __( 'weMail', 'wemail' );

        $this->init();
    }

    /**
     * Hook in events
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function init() {
        add_action( 'admin_init', array( $this, 'add_privacy_message' ) );
        add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ) );
        add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
    }

    /**
     * Adds the privacy policy content
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_privacy_message() {
        if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
            ob_start();
            require_once WEMAIL_INCLUDES . '/Privacy/privacy-policy-content.php';
            $content = ob_get_clean();

            /**
             * WeMail Privacy Policy content filter
             *
             * @since 1.0.0
             *
             * @var string
             */
            $content = apply_filters( 'wemail_privacy_policy_content', $content );

            if ( $content ) {
                wp_add_privacy_policy_content( $this->name, $content );
            }
        }
    }

    /**
     * Register exporter to list of data exporters
     *
     * @since 1.0.0
     *
     * @param array $exporters
     *
     * @return array
     */
    public function register_exporters( $exporters = array() ) {
        $exporters['wemail-subscriber-data'] = array(
            'exporter_friendly_name' => __( 'weMail Subscriber Data', 'wemail' ),
            'callback'               => array( $this, 'export_subscriber_data' ),
        );

        return $exporters;
    }

    /**
     * Find and export subscriber data
     *
     * @since 1.0.0
     *
     * @param string $email_address
     * @param int    $page
     *
     * @return array
     */
    public function export_subscriber_data( $email_address, $page ) {
        $data_to_export = array();

        wemail_set_owner_api_key();
        $subscriber = wemail()->subscriber->get( $email_address );

        if ( $subscriber ) {
            $data_to_export[] = array(
                'group_id'          => 'wemail_subscriber',
                'group_label'       => __( 'weMail Subscriber Data', 'wemail' ),
                'group_description' => __( 'The weMail subscriber data.', 'wemail' ),
                'item_id'           => 'wemail-subscriber',
                'data'              => $this->get_subscriber_data( $subscriber ),
            );
        }

        return array(
            'data' => $data_to_export,
            'done' => true,
        );
    }

    /**
     * Register data eraser to eraser list
     *
     * @since 1.0.0
     *
     * @param array $erasers
     *
     * @return array
     */
    public function register_eraser( $erasers = array() ) {
        $erasers['wemail-subscriber-data'] = array(
            'eraser_friendly_name' => __( 'weMail Subscriber Data', 'wemail' ),
            'callback'               => array( $this, 'erase_subscriber_data' ),
        );

        return $erasers;
    }

    /**
     * Find and erase subscriber data
     *
     * @since 1.0.0
     *
     * @param string $email_address
     * @param int    $page
     *
     * @return array
     */
    public function erase_subscriber_data( $email_address, $page ) {
        $response = array(
            'items_removed'  => false,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => true,
        );

        wemail_set_owner_api_key();
        $subscriber = wemail()->subscriber->get( $email_address );

        if ( $subscriber ) {
            $subscriber_data = $this->get_subscriber_data( $subscriber );

            foreach ( $subscriber_data as $data ) {
                /* translators: %s: search term */
                $response['messages'][] = sprintf( __( 'Removed subscriber %s', 'wemail' ), $data['name'] );
            }

            $response['items_removed'] = wemail()->subscriber->delete( $subscriber['id'], true );
        }

        return $response;
    }

    /**
     * Subscriber data with meta field
     *
     * @since 1.0.0
     *
     * @param array $subscriber
     *
     * @return array
     */
    protected function get_subscriber_data( $subscriber ) {
        $subscriber_data = array();

        $meta_fields = wemail()->api->subscribers()->meta()->get();
        $meta_fields = $meta_fields['data'];

        $ignore_fields = array( 'email' );

        foreach ( $meta_fields as $meta_field ) {
            $meta_name = $meta_field['name'];

            if ( isset( $subscriber[ $meta_name ] ) && ! in_array( $meta_name, $ignore_fields, true ) ) {
                $subscriber_data[] = array(
                    'name'  => $meta_field['title'],
                    'value' => $subscriber[ $meta_name ],
                );
            }
        }

        return $subscriber_data;
    }
}
