<?php
namespace App\Middlewares;

use Peanut\Phalcon\Mvc\Micro\Middleware;
use App\Traits\Auth;

class Validator extends Middleware
{
    use Auth;

    public function handle()
    {
        // validate header/formdata/body/query/path
        if ($errors = $this->validator->validate()) {
            $this->response->content([
                'status'  => '400',
                'message' => 'Bad Request',
                'errors'  => $errors,
            ])->send();

            return false;
        // validate authorization in header
        } elseif ($this->validator->authorization) {
            $response = $this->validate($this->validator->authorization);
            if ($response->getjsonContent()['status'] != 200) {
                $response->send();

                return false;
            } else {
            }
        }
    }
}
