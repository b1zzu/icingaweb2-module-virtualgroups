<?php

namespace Icinga\Module\Virtualgroups\Controllers;

use Icinga\Web\Url;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Monitoring\DataView\DataView;
use Icinga\Module\Virtualgroups\Backend\Ido\Query\VirtualgroupsummaryQuery;
use Icinga\Module\Virtualgroups\DataView\Virtualgroupsummary;

class IndexController extends Controller
{
    private $virtualGroups = array(
        "L1",
        "L2",
        "L3"
    );

    private $currentVirtualGroupName;

    private $currentVirtualGroupKey;

    private $nextVirtualGroupKey;

    private $currentVirtualGroupsFilters = array();

    public function indexAction()
    {
        $title = array();
        foreach ($this->virtualGroups as $name) {
            $filter = $this->getParam("_host_$name");
            if (isset($filter)) {
                $title[] = $name;
                $this->currentVirtualGroupsFilters["_host_$name"] = $filter;
            } else if (!isset($this->currentVirtualGroupName)) {
                $title[] = $name;
                $this->currentVirtualGroupName = $name;
                $this->currentVirtualGroupKey = sprintf("_host_%s", $this->currentVirtualGroupName);
            } else if (!isset($this->nextVirtualGroupKey)) {
                $this->nextVirtualGroupKey = sprintf("_host_%s", $name);
            }
        }

        $this->addTitleTab(
            'hostgroups',
            join(" > ", $title),
            $this->translate('List host groups')
        );

        $this->setAutorefreshInterval(12);


        $hostGroups = new Virtualgroupsummary(
            $this->backend->select(),
            $this->currentVirtualGroupKey,
            array(
                'hostgroup_alias',
                'hostgroup_name',
                'hosts_down_handled',
                'hosts_down_unhandled',
                'hosts_pending',
                'hosts_total',
                'hosts_unreachable_handled',
                'hosts_unreachable_unhandled',
                'hosts_up',
                'services_critical_handled',
                'services_critical_unhandled',
                'services_ok',
                'services_pending',
                'services_total',
                'services_unknown_handled',
                'services_unknown_unhandled',
                'services_warning_handled',
                'services_warning_unhandled'
            )
        );
        $this->applyRestriction('monitoring/filter/objects', $hostGroups);
        $this->filterQuery($hostGroups);

        $this->setupPaginationControl($hostGroups);
        $this->setupLimitControl();
        $this->setupSortControl(array(
            'hosts_severity'  => $this->translate('Severity'),
            'hostgroup_alias' => $this->translate('Host Group Name'),
            'hosts_total'     => $this->translate('Total Hosts'),
            'services_total'  => $this->translate('Total Services')
        ), $hostGroups);

        $this->view->hostGroups = $hostGroups;
        $this->view->virtualGroupKey = $this->currentVirtualGroupKey;
        $this->view->nextVirtualGroupKey = $this->nextVirtualGroupKey;
    }

    /**
     * Add tab to the page
     *
     * @param String $action
     * @param String $title
     * @param String $tip
     * @throws \Icinga\Exception\Http\HttpNotFoundException
     * @throws \Icinga\Exception\ProgrammingError
     */
    protected function addTitleTab($action, $title, $tip)
    {
        $this->getTabs()->add($action, array(
            'title' => $tip,
            'label' => $title,
            'url'   => Url::fromRequest()
        ))->activate($action);
        $this->view->title = $title;
    }

    /**
     * Apply filters on a DataView
     *
     * @param DataView $dataView The DataView to apply filters on
     *
     * @return DataView $dataView
     */
    protected function filterQuery(DataView $dataView)
    {
        $this->setupFilterControl($dataView, null, null, array(
            'format', // handleFormatRequest()
            'stateType', // hostsAction() and servicesAction()
            'addColumns', // addColumns()
            'problems' // servicegridAction()
        ));
        $this->handleFormatRequest($dataView);
        return $dataView;
    }
}
