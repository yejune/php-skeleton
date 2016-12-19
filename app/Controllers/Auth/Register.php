<?php
namespace App\Controllers\Auth;

use App\Traits\Auth;

class Register extends \App\Controllers
{
    use Auth;

    public function index()
    {
        $userId       = $this->request->getBody('user_id');
        $userName     = $this->request->getBody('user_name');
        $userPassword = $this->request->getBody('user_password');

        return $this->register($userId, $userName, $userPassword);
    }
}
