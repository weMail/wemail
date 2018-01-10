<?php

namespace WeDevs\WeMail\Traits;

trait Singleton
{
    /**
     * Singleton class instance holder
     *
     * @var object
     */
    private static $instance;

    /**
     * Make a class instance
     *
     * @return object
     */
    public static function instance()
    {
        if (! isset(self::$instance) && !(self::$instance instanceof self)) {
            self::$instance = new self;

            if (method_exists(self::class, 'boot')) {
                self::$instance->boot();
            }
        }


        return self::$instance;
    }
}
