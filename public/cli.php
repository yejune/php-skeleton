<?php
use \App\BootstrapCli;
use \Phalcon\Cli\Console;
use \Phalcon\Di\FactoryDefault\Cli as Di;

define('__BASE__', dirname(dirname(__FILE__)));
if (false === file_exists(__BASE__.'/vendor/autoload.php')) {
    exit(
        'You need to set up the project dependencies using the following commands:'.PHP_EOL.
        'wget http://getcomposer.org/composer.phar'.PHP_EOL.
        'php composer.phar install'.PHP_EOL
    );
}
try {
    include_once __BASE__.'/vendor/autoload.php';
    include_once __BASE__.'/app/Helpers/Function.php';
    $bootstrap = new BootstrapCli(new Di());
    $bootstrap->run(new \Phalcon\Cli\Console(), $argv);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage().PHP_EOL;
    exit(255);
}
