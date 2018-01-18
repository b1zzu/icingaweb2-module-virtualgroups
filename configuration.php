<?php

use Icinga\Application\Config;

/** @var \Icinga\Application\Modules\Module $this */
$section = $this->menuSection(N_('Overview'));

$menuName = Config::module('virtualgroups')->get('main', 'menu_name', '%s Overview');
$menuPriority = Config::module('virtualgroups')->get('main', 'menu_priority', 100);
$virtualGroups = Config::module('virtualgroups')->getSection('groups')->toArray();

foreach ($virtualGroups as $key => $name) {
    $section->add(sprintf($menuName, $name), array(
        'url'      => sprintf('virtualgroups?group=%s', $key),
        'priority' => $menuPriority
    ));
}
