<?php
namespace WeDevs\WeMail\Traits;

trait Hooker {

    /**
     * Hooks a function on to a specific action.
     *
     * @since 1.0.0
     *
     * @param string   $tag
     * @param callable $function
     * @param integer  $priority
     * @param integer  $accepted_args
     *
     * @return void
     */
    public function add_action( $tag, $function, $priority = 10, $accepted_args = 1 ) {
        add_action( $tag, array( $this, $function ), $priority, $accepted_args );
    }

    /**
     * Hooks a function on to a specific filter.
     *
     * @since 1.0.0
     *
     * @param string   $tag
     * @param callable $function
     * @param integer  $priority
     * @param integer  $accepted_args
     *
     * @return void
     */
    public function add_filter( $tag, $function, $priority = 10, $accepted_args = 1 ) {
        add_filter( $tag, array( $this, $function ), $priority, $accepted_args );
    }
}
