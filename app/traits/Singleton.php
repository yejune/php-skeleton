<?php
namespace App\Traits;

trait Singleton
{
    /**
     * The single instance
     * @var mixed
     */
    private static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }
}
