<?php
namespace App\Controllers\Auth;

use App\Traits\Auth;

class Validate extends \App\Controllers
{
    use Auth;

    public function index()
    {
        // spec에 security Bearer가 정의 되어 있고 endpoint가 인증을 필요로한다면
        // header의 authorization가 유효한지 확인한뒤 진입한다.
        // app/middlewares/Validator.php 참조
        // spec을 정확하게 정의하면 validation와 authorization 신경쓰지 않고 개발할수 있다.

        return $this->response->content([
            'status'  => '200',
            'message' => 'Ok',
        ]);
    }
}
