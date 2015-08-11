<?php

use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_DevicesController
 */
class HardwareLibrary_ComputersController extends Action
{
    /** @var  boolean */
    private $isAdmin;

    /** @var  \MPSToolbox\Legacy\Models\UserModel */
    protected $identity;

    public function init ()
    {
        $this->isAdmin = $this->view->IsAllowed(\MPSToolbox\Legacy\Models\Acl\AdminAclModel::RESOURCE_ADMIN_TONER_WILDCARD, \MPSToolbox\Legacy\Models\Acl\AppAclModel::PRIVILEGE_ADMIN);
        $this->identity            = Zend_Auth::getInstance()->getIdentity();
    }


    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->_pageTitle    = ['Computers'];
        $this->view->isAdmin = $this->isAdmin;
    }

    /**
     * Gets the list of devices for the hardware library "all devices" page
     */
    public function allDevicesListAction ()
    {
        $postData          = $this->getAllParams();
        $filterCanSell     = ($this->_getParam('filterCanSell', null) == 'true') ? true : false;
        $filterUnapproved  = ($this->_getParam('filterUnapproved', null) == 'true') ? true : false;
        $filterSearchIndex = $this->_getParam('filterSearchIndex', null);
        $filterSearchValue = $this->_getParam('filterSearchValue', null);
        $columnFactory     = new \Tangent\Grid\Order\ColumnFactory([
            'deviceName', 'oemSku', 'dealerSku', 'isSystemDevice'
        ]);

        $gridRequest        = new \Tangent\Grid\Request\JqGridRequest($postData, $columnFactory);
        $gridResponse       = new \Tangent\Grid\Response\JqGridResponse($gridRequest);
        $masterDeviceMapper = MasterDeviceMapper::getInstance();
        $dataAdapter        = new \MPSToolbox\Grid\DataAdapter\MasterDeviceDataAdapter($masterDeviceMapper, $filterCanSell);

        /**
         * Setup Filters
         */
        $filterCriteriaValidator = new Zend_Validate_InArray(['haystack' => ['deviceName', 'oemSku', 'dealerSku']]);


        if ($filterSearchIndex !== null && $filterSearchValue !== null && $filterCriteriaValidator->isValid($filterSearchIndex))
        {
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\Contains($filterSearchIndex, $filterSearchValue));
        }

        if ($filterUnapproved)
        {
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\IsNot('isSystemDevice', true));
        }

        /**
         * Setup grid
         */
        $gridService = new \Tangent\Grid\Grid($gridRequest, $gridResponse, $dataAdapter);
        $this->sendJson($gridService->getGridResponseAsArray());

        return;
    }

    public function loadFormsAction ()
    {
        $this->_helper->layout()->disableLayout();
        $hardwareId = $this->_getParam('hardwareId', false);

        $hardware = \MPSToolbox\Entities\ExtComputerEntity::find($hardwareId);
        $isAllowed    = ((!$hardware instanceof \MPSToolbox\Entities\ExtComputerEntity || !$hardware->getIsSystemDevice() || $this->isAdmin) ? true : false);

        $service = new \MPSToolbox\Services\HardwareService($hardwareId, $this->identity->dealerId, $isAllowed, $this->isAdmin);

        if ($hardware instanceof \MPSToolbox\Entities\ExtComputerEntity)
        {
            $this->view->modelName      = $hardware->getModelName();
            $this->view->manufacturerId = $hardware->getManufacturer()->getId();
        }

        $forms = $service->getForms();

        foreach ($forms as $formName => $form) {
            $this->view->$formName = $form;
        }

        $this->view->isAllowed                   = $isAllowed;
        $this->view->manufacturers               = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper::getInstance()->fetchAllAvailableManufacturers();
        $this->view->hardware                = $hardware;
        $this->view->isMasterDeviceAdministrator = $this->isAdmin;
    }

}