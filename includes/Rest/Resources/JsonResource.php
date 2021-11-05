<?php
namespace WeDevs\WeMail\Rest\Resources;

abstract class JsonResource {

    protected $rest_keys = false;

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
     * @return mixed
     */
    public static function single( $resource ) {
        return ( new static() )->blueprint( $resource );
    }

    /**
     * Transform collection of items
     *
     * @param array $resources
     * @return array
     */
    public static function collection( array $resources ) {
        $instance = new static();

        $data = array_map( [ $instance, 'blueprint' ], $resources );

        if ( $instance->rest_keys ) {
            $data = array_values( $data );
        }

        return $data;
    }
}
