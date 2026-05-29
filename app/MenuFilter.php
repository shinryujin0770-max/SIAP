<?php

namespace App;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Laratrust\Laratrust;

class MenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if (isset($item['permission'])) {
            $item['restricted'] = true;
        }

        return $item;
    }
}
