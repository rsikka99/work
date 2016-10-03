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

    public function onlineAction() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->query('update devices set `online`=? where dealerId=? and masterDeviceId=?', [
            $this->getParam('online')=='true'?'1':'0',
            \Zend_Auth::getInstance()->getIdentity()->dealerId,
            intval($this->getParam('id'))
        ])->execute();
        $this->sendJson(array('ok'));
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
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $postData          = $this->getAllParams();
        $filterCanSell     = ($this->_getParam('filterCanSell', null) == 'true') ? true : false;
        $filterPriced     = ($this->_getParam('filterPriced', null) == 'true') ? true : false;
        $filterUnapproved  = ($this->_getParam('filterUnapproved', null) == 'true') ? true : false;
        $filterIncomplete  = ($this->_getParam('filterIncomplete', null) == 'true') ? true : false;
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
        if ($filterPriced)
        {
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\In('master_devices.id',"
                select masterDeviceId from devices where dealerId={$dealerId} and sellPrice>0
            "));
        }
        if ($filterIncomplete)
        {
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\NotIn('master_devices.id',
                'select master_device_id
  from device_toners dt
    join master_devices msub on dt.master_device_id=msub.id
    join toners t on dt.toner_id=t.id and t.manufacturerId = msub.manufacturerId'
            ));
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\In('master_devices.id',
                'select id from master_devices where imageFile is null'
            ));
            $dataAdapter->addFilter(new \Tangent\Grid\Filter\In('master_devices.id',
                'select id from master_devices where isA3=0 and isAccessCard=0 and isADF=0 and isBinding=0 and isCapableOfReportingTonerLevels=0 and isDuplex=0 and isFax=0 and isPIN=0 and isSmartphone=0 and isStapling=0 and isTouchscreen=0 and isUSB=0 and isWalkup=0 and isWired=0 and isWireless=0'
            ));
        }

        /**
         * Setup grid
         */
        $gridService = new \Tangent\Grid\Grid($gridRequest, $gridResponse, $dataAdapter);
        $this->sendJson($gridService->getGridResponseAsArray());
    }

    /**
     * Handles creating a new master device
     * @Deprecated
     */
    public function createAction ()
    {
        $this->sendJson(['message' => 'This is action is Deprecated.']);
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