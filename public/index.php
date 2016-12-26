<?php
use \App\Bootstrap;
use \Peanut\Phalcon\Mvc\Micro;
use \Phalcon\DI\FactoryDefault as Di;

define('__BASE__', dirname(dirname(__FILE__)));

if (false === file_exists(__BASE__.'/vendor/autoload.php')) {
    exit(
        'You need to set up the project dependencies using the following commands:'.PHP_EOL.
        'wget http://getcomposer.org/composer.phar'.PHP_EOL.
        'php composer.phar install'.PHP_EOL
    );
}

// Redirect http access to https
if (
    false === (true === isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
    &&
    false === (true === isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
) {
    $redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$redirect);
    exit();
}

try {
    include_once __BASE__.'/vendor/autoload.php';
    include_once __BASE__.'/app/Helpers/Function.php';

    $bootstrap = new Bootstrap(new Di());
    $bootstrap(new Micro())->handle();
} catch (\Throwable $e) {
    throw new \App\Exception($e);
}
