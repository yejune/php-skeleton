<?php
namespace App;

class Exception extends \Exception
{
    public function __construct($e)
    {
        \Phalcon\Di::getDefault()->getShared('response')->content([
            'status'  => '500',
            'message' => $e->getMessage(),
        ])->send();
        die();
    }
}
