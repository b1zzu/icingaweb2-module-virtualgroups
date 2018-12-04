<?php

namespace Icinga\Module\Virtualgroups\Controllers;

use Icinga\Application\Config;
use Icinga\Web\Form;
use Icinga\Web\Url;
use Icinga\Web\Widget\Tabextension\DashboardAction;
use Icinga\Web\Widget\Tabextension\MenuAction;
use Icinga\Web\Widget\Tabextension\OutputFormat;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Monitoring\DataView\DataView;
use Icinga\Module\Virtualgroups\DataView\Virtualgroupsummary;

class IndexController extends Controller
{
    private $virtualGroups = array();

    private $currentVirtualGroupKey;

    private $nextVirtualGroupKey;

    public function indexAction()
    {
        $this->createTabs();

        $this->loadVirtualGroups();

        $_filters = array();
        foreach ($this->virtualGroups as $key => $name) {
            $filter = $this->getParam($key);
            if (isset($filter)) {
                $_filters[] = "$name ( $filter ) ";
                $this->view->enableBack = true;
            } else if (!isset($this->currentVirtualGroupKey)) {
                $this->currentVirtualGroupKey = $key;
            } else if (!isset($this->nextVirtualGroupKey)) {
                $this->nextVirtualGroupKey = $key;
            }
        }

        $this->setupGroupControl();

        $title = sprintf("%s# %s", join("& ", $_filters), $this->virtualGroups[$this->currentVirtualGroupKey]);

        $this->addTitleTab(
            'hostgroups',
            $title,
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

        // do not use getUrlParams of Filter object that urlencodes params
        // we need raw values so we use parse_str instead
        parse_str($this->view->filterEditor->getFilter()->toQueryString(), $paramsDecoded);
        $this->view->paramsDecoded = $paramsDecoded;
    }

    /**
     * Add extended tabs for controller
     */
    private function createTabs()
    {
        $this->getTabs()->extend(new OutputFormat())->extend(new DashboardAction())->extend(new MenuAction());
    }

    /**
     *  Load virtual groups list from config file
     */
    protected function loadVirtualGroups()
    {
        $this->virtualGroups = Config::module('virtualgroups')->getSection('groups')->toArray();
    }

    /**
     * Create the group dropdown for the view
     *
     * @throws \Zend_Form_Exception
     */
    protected function setupGroupControl()
    {
        $this->currentVirtualGroupKey = $this->getParam("group", $this->currentVirtualGroupKey);

        $groupForm = new Form();
        $groupForm->setTokenDisabled();
        $groupForm->setUidDisabled();
        $groupForm->setAttrib('class', 'inline');
        $groupForm->setMethod("GET");
        $groupForm->addElement(
            'select',
            'group',
            array(
                'autosubmit'   => true,
                'label'        => 'Group by',
                'multiOptions' => $this->virtualGroups,
                'decorators'   => array(
                    array('ViewHelper'),
                    array('Label')
                )
            )
        );
        $groupForm->populate(array('group' => $this->currentVirtualGroupKey));

        $this->view->groupControl = $groupForm;
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
            'problems', // servicegridAction()
            'group',
        ));
        $this->handleFormatRequest($dataView);
        return $dataView;
    }

}
