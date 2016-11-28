<?php
namespace App;

class Exception extends \Exception
{
    public function __construct($e, $status = '500')
    {
        if (true === is_object($e)) {
            $e = $e->getMessage();
        }
        \Phalcon\Di::getDefault()->getShared('response')->content([
            'status'  => $status,
            'message' => $e,
        ])->send();
        die();
    }
}
