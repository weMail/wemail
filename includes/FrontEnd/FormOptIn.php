<?php
namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\Traits\Hooker;

class FormOptIn {
    use Hooker;

    /**
     * @var $settings
     */
    protected $settings;

    /**
     *
     * FormOptIn constructor.
     */
    public function __construct() {
        wemail_set_owner_api_key( false );

        $this->settings = get_option(
            'wemail_general',
            array(
                'registration_enabled' => false,
                'registration_label'   => '',
                'registration_list_id' => '',
                'woocommerce_enabled'  => false,
                'woocommerce_label'    => '',
                'woocommerce_list_id'  => '',
                'comment_enabled'      => false,
                'comment_label'        => '',
                'comment_list_id'      => '',
                'form_in_blogs'        => false,
                'subscription_form_id' => '',
            )
        );

        $this->registration_hooks();
    }

    /**
     *  Register Hooks
     */
    protected function registration_hooks() {
        if ( wemail_validate_boolean( $this->settings['registration_enabled'] ) ) {
            if ( is_multisite() ) {
                $this->add_action( 'signup_extra_fields', 'subscribe_checkbox_field_multisite' );
                $this->add_action( 'after_signup_user', 'save_subscriber_from_registration' );
            } else {
                $this->add_action( 'register_form', 'registration_opt_in_label' );
                $this->add_action( 'user_register', 'save_subscriber_from_registration' );
            }
        }

        if ( wemail_validate_boolean( $this->settings['woocommerce_enabled'] ) && class_exists( 'WooCommerce' ) ) {
            $this->add_filter( 'woocommerce_billing_fields', 'add_subscribe_field_woocommerce_billing_form' );
            $this->add_filter( 'woocommerce_new_order', 'save_subscriber_from_woocommerce_billing' );
        }

        if ( wemail_validate_boolean( $this->settings['comment_enabled'] ) ) {
            $this->add_action( 'comment_form_logged_in_after', 'comment_opt_in_label' );
            $this->add_action( 'comment_form_after_fields', 'comment_opt_in_label' );
            $this->add_action( 'comment_post', 'save_subscriber_from_comment' );
        }

        if ( wemail_validate_boolean( $this->settings['form_in_blogs'] ) ) {
            $this->add_filter( 'the_content', 'wemail_form_footer' );
        }
    }

    public function wemail_form_footer( $content ) {
        return $content . wemail_form( $this->settings['subscription_form_id'] );
    }

    /**
     * Registration opt-in label
     */
    public function registration_opt_in_label() {
        $this->subscribe_checkbox_field( 'registration_label' );
    }

    /**
     * Comment opt-in label
     */
    public function comment_opt_in_label() {
        $this->subscribe_checkbox_field( 'comment_label' );
    }

    /**
     *  Show Field.
     *
     * @param $label_index
     */
    public function subscribe_checkbox_field( $label_index ) {
        ?>
            <p class="wemail-form-opt-in-field">

                <input id="wemail_form_opt_in" <?php /** phpcs:ignore WordPress.Security.NonceVerification.Missing **/ echo isset( $_POST['wemail_form_opt_in'] ) ? 'checked' : ''; ?> type="checkbox" name="wemail_form_opt_in">
                <label for="wemail_form_opt_in"><?php /** phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText */ esc_html_e( $this->settings[ $label_index ], 'wemail' ); ?></label>
            </p>
        <?php
    }

    /**
     *  Show Field on Multisite.
     */
    public function subscribe_checkbox_field_multisite() {
        $registration_label = $this->settings['registration_label'];
        ?>
            <p class="wemail-form-opt-in-field">
                <label>
                    <input id="wemail_form_opt_in" <?php /** phpcs:ignore WordPress.Security.NonceVerification.Missing **/ echo isset( $_POST['wemail_form_opt_in'] ) ? 'checked' : ''; ?> type="checkbox" name="wemail_form_opt_in">
                    <?php /** phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText */ esc_html_e( $registration_label, 'wemail' ); ?>
                </label>
            </p>
        <?php
    }

    /**
     *  Add field on woocommerce billing section.
     *
     * @param $fields
     * @return mixed
     */
    public function add_subscribe_field_woocommerce_billing_form( $fields ) {
        $fields['wemail_form_opt_in'] = array(
            'type'  => 'checkbox',
            'label' => $this->settings['woocommerce_label'],
        );

        return $fields;
    }

    /**
     *  Save subscriber from registration field.
     *
     * @param $user_id
     */
    public function save_subscriber_from_registration( $user_id ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( isset( $_POST['wemail_form_opt_in'] ) ) {
            wemail()->subscriber->createOrUpdate(
                array(
                    'email'   => filter_input( INPUT_POST, 'user_email', FILTER_VALIDATE_EMAIL ),
                    'list_id' => $this->settings['registration_list_id'],
                )
            );
        }
    }

    /**
     *  Save subscriber from comment.
     *
     * @param $comment_id
     */
    public function save_subscriber_from_comment( $comment_id ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( isset( $_POST['wemail_form_opt_in'] ) ) {
            $data = array(
                'list_id' => $this->settings['comment_list_id'],
                'email'   => get_comment_author_email( $comment_id ),
            );

            $name = explode( ' ', get_comment_author( $comment_id ), 2 );

            if ( count( $name ) === 2 ) {
                $data['first_name'] = $name[0];
                $data['last_name'] = $name[1];
            } else {
                $data['first_name'] = $name[0];
            }

            wemail()->subscriber->createOrUpdate( $data );
        }
    }

    /**
     *  Save subscriber from woocommerce billing.
     *  @param $order_id
     */
    public function save_subscriber_from_woocommerce_billing( $order_id ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( isset( $_POST['wemail_form_opt_in'] ) ) {
            $order = wc_get_order( $order_id );

            wemail()->subscriber->createOrUpdate(
                array(
                    'first_name' => $order->get_billing_first_name(),
                    'last_name'  => $order->get_billing_last_name(),
                    'email'      => $order->get_billing_email(),
                    'phone'      => $order->get_billing_phone(),
                    'address1'   => $order->get_billing_address_1(),
                    'address2'   => $order->get_billing_address_2(),
                    'city'       => $order->get_billing_city(),
                    'state'      => $order->get_billing_state(),
                    'country'    => $order->get_billing_country(),
                    'zip'        => $order->get_billing_postcode(),
                    'list_id'    => $this->settings['woocommerce_list_id'],
                )
            );
        }
    }
}
