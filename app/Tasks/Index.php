<?php
namespace App\Tasks;

use App\Models\Databases\Api\User;
use App\Models\Databases\Api\Deploy;
use App\Models\Databases\Api\Command;
use App\Models\Databases\Api\Build;
use Symfony\Component\Process\Process;

class Index extends \Phalcon\Cli\Task
{
    public function index()
    {
        echo 'index';
    }
}
