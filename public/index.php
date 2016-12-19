<?php
use \App\Bootstrap;
use \Peanut\Phalcon\Mvc\Micro;
use \Phalcon\DI\FactoryDefault as Di;

try {
    define('__BASE__', dirname(dirname(__FILE__)));

    if (false === file_exists(__BASE__.'/vendor/autoload.php')) {
        die(
            'You need to set up the project dependencies using the following commands:'.PHP_EOL.
            'wget http://getcomposer.org/composer.phar'.PHP_EOL.
            'php composer.phar install'.PHP_EOL
        );
    }
    include_once __BASE__.'/vendor/autoload.php';
    include_once __BASE__.'/app/Helpers/Function.php';

    $bootstrap = new Bootstrap(new Di());
    $bootstrap(new Micro())->handle();
} catch (\Throwable $e) {
    throw new \App\Exception($e);
}
