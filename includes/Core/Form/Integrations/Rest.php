<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use Stringy\StaticStringy;
use WeDevs\WeMail\RestController;

class Rest extends RestController {

    /**
     * REST Base
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $rest_base = 'forms/integrations';

    /**
     * Holds the integration instances
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $integrations = [];

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->register_routes();
    }

    /**
     * Magic method to create integration instances on demand
     *
     * @since 1.0.0
     *
     * @param  string $prop
     *
     * @return void|object
     */
    public function __get( $prop ) {
        $integration = StaticStringy::underscored( $prop );

        if ( array_key_exists( $integration, $this->integrations ) ) {
            return $this->integrations[$integration];

        } else if ( array_key_exists( $integration, wemail()->form->integrations() ) ) {
            $class_name = StaticStringy::upperCamelize( $integration );
            $integration_class = "\\WeDevs\\WeMail\\Core\\Form\\Integrations\\$class_name";

            if ( class_exists( $integration_class ) ) {
                $this->integrations[$integration] = $integration_class::instance();

                return $this->integrations[$integration];
            }
        }
    }

    /**
     * Register REST routes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_routes() {
        $this->get( '/{name}/forms', 'forms', 'can_create_form' );
        $this->post( '/{name}', 'save', 'can_create_form' );
    }

    /**
     * Get integration forms
     *
     * @since 1.0.0
     *
     * @param  \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function forms( $request ) {
        $integration = $request->get_param( 'name' );

        if ( ! $this->$integration->is_active ) {
            return $this->$integration->inactivity_message();
        }

        $forms = $this->$integration->forms();

        if ( is_wp_error( $forms ) ) {
            return $forms;
        }

        return $this->respond( $forms );
    }

    /**
     * Save integration settings
     *
     * @since 1.0.0
     *
     * @param  \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function save( $request ) {
        $integration = $request->get_param( 'name' );
        $integration = StaticStringy::underscored( $integration );

        if ( ! $this->$integration->is_active ) {
            return $this->$integration->inactivity_message();
        }

        $data = $request->get_param( 'settings' );

        $saved = $this->$integration->save( $data );

        if ( is_wp_error( $saved ) ) {
            return $saved;
        }

        return $this->respond( $saved );
    }
}
