<?php
namespace App\Controllers\Auth;

use App\Traits\Auth;

class Refresh extends \App\Controllers
{
    use Auth;

    public function index()
    {
        $refresh = $this->request->getBody('refresh');

        return $this->$refresh($refresh);
    }
}
