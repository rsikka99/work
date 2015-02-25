<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\MasterDeviceEntity;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\MasterDeviceService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use Tangent\Controller\Action;

/**
 * Class Api_DevicesController
 *
 * This controller handles everything to do with creating/updating normal devices
 */
class Api_DevicesController extends Action
{

    /**
     *
     */
    public function indexAction ()
    {
        $masterDeviceId = $this->getParam('masterDeviceId', false);

        if ($masterDeviceId !== false)
        {
            $masterDeviceId = $this->getParam('masterDeviceId', false);

            $this->sendJson(MasterDeviceEntity::with('manufacturer')->find($masterDeviceId)->toArray());
        }
        else
        {
            $searchTerm = $this->getParam('q', false);
            $pageLimit  = $this->getParam('page_limit', 10);
            $page       = $this->getParam('page', 1);

            $query = MasterDeviceEntity::join('manufacturers', 'manufacturers.id', '=', 'master_devices.manufacturerId')
                                       ->with('manufacturer')
                                       ->orderBy('manufacturers.displayname', 'ASC')
                                       ->orderBy('master_devices.modelName', 'ASC')
                                       ->select(['master_devices.id', 'master_devices.modelName', 'master_devices.manufacturerId'])
                                       ->limit($pageLimit);

            if (strlen($searchTerm) > 0)
            {
                $query->where('modelName', 'LIKE', '%' . implode('%', explode(' ', $searchTerm)) . '%');
            }

            $count = $query->count();

            if ($page > 1)
            {
                $query->offset($pageLimit * ($page - 1));
            }

            $this->sendJson([
                'total'         => $count,
                'masterDevices' => $query->get()->toArray(),
            ]);
        }
    }

    /**
     * Handles listing all the devices for a given jQuery Grid
     *
     * Takes the following parameters:
     *
     * @internal param $filterCanSell
     * @internal param $filterUnapproved
     * @internal param $filterSearchIndex Used to determine which column to perform a search on. Valid Columns are:
     *           - deviceName
     *           - oemSku
     *           - dealerSku
     *
     * @internal param $filterSearchValue
     */
    public function gridListAction ()
    {
        $postData          = $this->getAllParams();
        $filterCanSell     = ($this->_getParam('filterCanSell', null) == 'true') ? true : false;
        $filterUnapproved  = ($this->_getParam('filterUnapproved', null) == 'true') ? true : false;
        $filterSearchIndex = $this->_getParam('filterSearchIndex', null);
        $filterSearchValue = $this->_getParam('filterSearchValue', null);

        $columnFactory = new \Tangent\Grid\Order\ColumnFactory([
            'deviceName', 'oemSku', 'dealerSku', 'isSystemDevice'
        ]);

        $gridRequest  = new \Tangent\Grid\Request\JqGridRequest($postData, $columnFactory);
        $gridResponse = new \Tangent\Grid\Response\JqGridResponse($gridRequest);

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
    }

    /**
     * Handles creating a new master device
     */
    public function createAction ()
    {
        if ($this->getRequest()->isPost())
        {
            $postData            = $this->getRequest()->getPost();
            $masterDeviceService = new MasterDeviceService();
            $result              = $masterDeviceService->saveMasterDevice($postData);

            if ($result)
            {
                $this->sendJson([
                    'message' => 'Master device saved successfully',
                    'success' => true,
                ]);
            }
            else
            {
                $this->sendJsonError('There was an error saving the master device');
            }
        }

        $this->sendJsonError('Invalid GET request. Please use POST.');
    }

    /**
     *
     */
    public function deleteAction ()
    {
        $this->sendJson(['message' => 'This is action is not implemented yet.']);
    }

    /**
     *
     */
    public function saveAction ()
    {
        $this->sendJson(['message' => 'This is action is not implemented yet.']);
    }

    /**
     *
     */
    public function viewAction ()
    {
        $masterDeviceId = $this->getParam('deviceId', false);

        $masterDevice = MasterDeviceEntity::with([
            'Manufacturer',
            'TonerConfiguration',
            'Toners',
            'Toners.Manufacturer',
            'Toners.TonerColor',
            'DealerMasterDeviceAttributes' => function ($query)
            {
                $query->where('dealerId', '=', Zend_Auth::getInstance()->getIdentity()->dealerId);
            },
        ])->find($masterDeviceId);

        if (!$masterDevice)
        {
            $this->sendJsonError('A device with that ID does not exist.');
        }

        $this->sendJson($masterDevice->toArray());
    }

    public function viewTonersAction ()
    {
        $masterDeviceId = $this->getParam('deviceId', false);

        $masterDevice = MasterDeviceEntity::with([
            'Toners',
            'Toners.Manufacturer',
            'Toners.TonerColor',
        ])->find($masterDeviceId);

        if (!$masterDevice)
        {
            $this->sendJsonError('A device with that ID does not exist.');
        }

        $this->sendJson(['data' => $masterDevice->toners]);
    }
}