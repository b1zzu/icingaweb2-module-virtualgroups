<?php

use Icinga\Application\Config;

/** @var \Icinga\Application\Modules\Module $this */
$section = $this->menuSection(N_('Overview'));

$menuName = Config::module('virtualgroups')->get('main', 'menu_name', 'Virtual Groups Hello');
$menuPriority = Config::module('virtualgroups')->get('main', 'menu_priority', 100);

$section->add($menuName, array(
    'url'      => 'virtualgroups',
    'priority' => $menuPriority
));
