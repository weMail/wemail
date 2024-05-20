<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\RestController;
use WeDevs\WeMail\Traits\Stringy;

class Rest extends RestController {
    use Stringy;

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
     * Magic method to create integration instances on demand
     *
     * @since 1.0.0
     *
     * @param  string $prop
     *
     * @return void|object
     */
    public function __get( $prop ) {
        $integration = $this->underscored( $prop );

        if ( array_key_exists( $integration, $this->integrations ) ) {
            return $this->integrations[ $integration ];
        } elseif ( array_key_exists( $integration, wemail()->form->integrations() ) ) {
            $class_name = $this->upperCamelize( $integration );
            $integration_class = "\\WeDevs\\WeMail\\Core\\Form\\Integrations\\$class_name";
            if ( class_exists( $integration_class ) ) {
                $this->integrations[ $integration ] = $integration_class::instance();

                return $this->integrations[ $integration ];
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
        $this->get( '/', 'integrations', 'can_create_form' );
        $this->get( '/{name}/forms', 'forms', 'can_create_form' );
        $this->post( '/{name}', 'save', 'can_create_form' );
    }

    /*
     * Get all integrations with status
     */
    public function integrations( $request ) {
        $integrations = [];
        foreach ( wemail()->form->integrations() as $key => $integration ) {
            $integrations[] = [
                'slug' => $key,
                'is_active' => $this->$key->is_active,
            ];
        }

        return $integrations;
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
        $integration = $this->underscored( $integration );

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
