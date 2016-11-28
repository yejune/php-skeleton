<?php
namespace App\Traits;

trait Template
{
    /**
     * @param  array|string $define
     * @param  array        $assign
     * @return string
     */
    public function show($define, $assign = [])
    {
        if (true === is_string($define)) {
            $define = ['layout' => $define];
        }

        if (true === isset($this->title)) {
            $assign['title'] = $this->title;
        } else {
            $assign['title'] = '';
        }

        $template = $this->template;
        $template->define($define);
        $template->assign($assign);

        return $template->show('layout');
    }
}
