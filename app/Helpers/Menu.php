<?php
namespace App\Helpers;

class Menu
{
    public $sequence = 0;
    public $menu     = [];
    public $url      = '';

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function add($name, $url, $parent = 0)
    {
        $this->sequence++;
        $checked = false;
        if ($this->url == $url) {
            $checked = true;
        }
        if ($parent) {
            if ($checked) {
                $this->menu[$parent]['checked'] = true;
            }
            $this->menu[$parent]['children'][$this->sequence] = [
                'name'     => $name,
                'url'      => $url,
                'checked'  => $checked,
            ];
        } else {
            $this->menu[$this->sequence] = [
                'name'     => $name,
                'url'      => $url,
                'checked'  => $checked,
                'children' => [],
            ];
        }

        return $this->sequence;
    }
}
