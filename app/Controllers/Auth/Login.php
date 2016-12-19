<?php
namespace App\Controllers\Auth;

use App\Traits\Auth;

class Login extends \App\Controllers
{
    use Auth;

    public function index()
    {
        $userId       = $this->request->getBody('user_id');
        $userPassword = $this->request->getBody('user_password');

        return $this->login($userId, $userPassword);
    }
}
