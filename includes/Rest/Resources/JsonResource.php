<?php

namespace WeDevs\WeMail\Rest\Resources;

abstract class JsonResource {

    protected $reset_keys = false;

    /**
     * Blueprint of a resource
     *
     * @param $resource
     *
     * @return mixed
     */
    abstract public function blueprint( $resource );

    /**
     * Transform single item
     *
     * @param $resource
     *
     * @return mixed
     */
    public static function single( $resource ) {
        return ( new static() )->blueprint( $resource );
    }

    /**
     * Transform collection of items
     *
     * @param array $resources
     *
     * @return array
     */
    public static function collection( array $resources ) {
        $instance = new static();

        $data = array_map( [ $instance, 'blueprint' ], $resources );

        if ( $instance->reset_keys ) {
            $data = array_values( $data );
        }

        return $data;
    }

    /**
     * Check is it refund item
     *
     * @param $type
     *
     * @return bool
     */
    public function is_refund( $type ) {
        return $type === 'refund';
    }

    /**
     * Check item status is completed
     *
     * @param $status
     *
     * @return bool
     */
    public function is_completed( $status ) {
        return $status === 'completed';
    }
}
