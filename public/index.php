<?php
use \App\Bootstrap;
use \Peanut\Phalcon\Mvc\Micro;
use \Phalcon\DI\FactoryDefault as Di;

try {
    define('__BASE__', dirname(dirname(__FILE__)));

    include_once __BASE__.'/vendor/autoload.php';
    include_once __BASE__.'/app/helpers/function.php';

    $bootstrap = new Bootstrap(new Di());
    $bootstrap(new Micro())->handle();
} catch (\Throwable $e) {
    throw new \App\Exception($e);
}
