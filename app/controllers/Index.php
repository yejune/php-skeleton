<?php
namespace App\Controllers;

use App\Traits\Template;

class Index extends \App\Controllers
{
    use Template;

    public function index()
    {
        $request  = $this->request;
        $response = $this->response;

        $assign   = [
            'message'  => 'hello world',
        ];

        return $response->setContent(
            $this->show([
                'layout'   => './layout/normal.tpl',
                'contents' => './contents/main.tpl',
            ], $assign)
        );
    }

    public function info()
    {
        phpinfo();
    }

    public function pet()
    {
    }
}
