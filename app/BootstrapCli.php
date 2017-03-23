<?php
namespace App;

class BootstrapCli extends Bootstrap
{
    private $arguments;
    /**
     * @param  \Phalcon\Cli\Console   $app
     * @param  array                  $arguments
     * @return \Phalcon\Cli\Console
     */
    public function run(\Phalcon\Cli\Console $app, array $argv)
    {
        $this->initDispatcher();
        $this->initArguments($argv);
        $this->initDatabase();
        $app->setDI($this->di);

        return $app->handle($this->arguments);
    }
    private function initDispatcher()
    {
        $this->di->setShared('dispatcher', function () {
            $dispatcher = new \Peanut\Phalcon\Cli\Dispatcher();
            $dispatcher->setTaskSuffix('');
            $dispatcher->setActionSuffix('');

            return $dispatcher;
        });
    }
    private function die($message = '')
    {
        if ($message) {
            die($this->output($message));
        }
        die();
    }
    private function output($message)
    {
        echo $message.PHP_EOL;
    }
    private function initArguments(array $argv)
    {
        $this->arguments = [
            'task'   => 'Index',
            'action' => 'index',
        ];
        foreach ($argv as $k => $arg) {
            switch ($k) {
                case 0: break;
                case 1:
                    $this->arguments['task'] = $arg;
                    break;
                case 2:
                    $this->arguments['action'] = $arg;
                    break;
                default:
                    $this->arguments['params'][] = $arg;
           }
        }

        if (!array_key_exists('task', $this->arguments)) {
            $this->die('task required! usage: php public/cli.php [TASK NAME]');
        }
        $task = array_reduce(explode('/', $this->arguments['task']), function ($carry, $value) {
            return $carry.'\\'.ucfirst($value);
        }, '');
        $this->arguments['task'] = '\\App\\Tasks'.$task;
    }
}
