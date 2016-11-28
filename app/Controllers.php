<?php
namespace App;

class Controllers extends \Phalcon\Mvc\Controller
{
    public function onConstruct()
    {
        $menu = new \App\Helpers\Menu($this->request->getRewriteUri());

        $seq = $menu->add('Internal', '');
        $menu->add('hello', '/', $seq);
        $menu->add('readme', '/readme', $seq);

        $seq = $menu->add('External', '');
        $menu->add('google', 'http://google.com', $seq);
        $menu->add('naver', 'http://naver.com', $seq);

        $this->template->assign(['menu' => $menu->menu]);
        //\Peanut\Store::set('menu', $menu->menu);
    }
}
