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
            'message'  => 'Hello World!',
        ];

        return $response->setContent(
            $this->show([
                'layout'   => './layout/bootstrap.tpl',
                'contents' => './contents/main.tpl',
            ], $assign)
        );
    }

    public function info()
    {
        phpinfo();
    }

    public function readme()
    {
        $markdownText = file_get_contents(__BASE__.'/README.md');
        $html         = (new \Parsedown)->text($markdownText);

        $request  = $this->request;
        $response = $this->response;

        $assign   = [
            'message'  => $html,
        ];

        return $response->setContent(
            $this->show([
                'layout'   => './layout/bootstrap.tpl',
                'contents' => './contents/main.tpl',
            ], $assign)
        );
    }
}
