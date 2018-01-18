<?php

use Icinga\Module\Businessprocess\Storage\LegacyStorage;

/** @var \Icinga\Application\Modules\Module $this */
$section = $this->menuSection(N_('Overview'));

$section->add(N_('Virtual Groups'), array(
    'url'      => 'virtualgroups',
    'priority' => 100
));
