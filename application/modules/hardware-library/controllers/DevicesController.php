<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\TonerColorEntity;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\TonerService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use Tangent\Controller\Action;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;

/**
 * Class HardwareLibrary_DevicesController
 */
class HardwareLibrary_DevicesController extends Action
{
    /**
     * @var bool
     */
    protected $isMasterHardwareAdmin;

    public function init ()
    {
        $this->isMasterHardwareAdmin = $this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
    }

    /**
     * Handles mapping a toner to a device
     */
    public function addTonerAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $userId         = Zend_Auth::getInstance()->getIdentity()->id;
                $dealerId       = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $masterDeviceId = $this->getParam('masterDeviceId', false);
                $tonerId        = $this->getParam('tonerId', false);

                $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
                if (!$masterDevice instanceof MasterDeviceModel)
                {
                    $this->sendJsonError(sprintf('Invalid master device id'));
                }

                $toner = TonerMapper::getInstance()->find($tonerId);
                if (!$toner instanceof TonerModel)
                {
                    $this->sendJsonError(sprintf('Invalid toner id'));
                }

                /**
                 * Ensure we're only adding toners that fit into the configuration of the device
                 */
                $validTonerColors = \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel::getRequiredTonersForTonerConfig($masterDevice->tonerConfigId);
                if (!in_array((int)$toner->tonerColorId, $validTonerColors))
                {
                    $this->sendJsonError(sprintf('You cannot add a %s toner to this device.', TonerColorEntity::$ColorNames[(int)$toner->tonerColorId]));
                }

                $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);
                $tonerService->mapToner($tonerId, $masterDeviceId);

                /**
                 * Send success message
                 */
                $this->sendJson([
                    'message' => 'Toner mapped successfully',
                ]);
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson([
                    'message' => 'Unhandled Exception',
                    'uid'     => \Tangent\Logger\Logger::getUniqueId(),
                ]);
            }
        }
        else
        {
            $this->sendJsonError('Invalid Method');
        }
    }

    /**
     * Handles removing a toner from a device
     */
    public function removeTonerAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $userId         = Zend_Auth::getInstance()->getIdentity()->id;
                $dealerId       = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $masterDeviceId = $this->getParam('masterDeviceId', false);
                $tonerId        = $this->getParam('tonerId', false);

                $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
                if (!$masterDevice instanceof MasterDeviceModel)
                {
                    $this->sendJsonError(sprintf('Invalid master device id'));
                }

                $toner = TonerMapper::getInstance()->find($tonerId);
                if (!$toner instanceof TonerModel)
                {
                    $this->sendJsonError(sprintf('Invalid toner id'));
                }

                $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);
                $tonerService->unmapToner($tonerId, $masterDeviceId);

                /**
                 * Send success message
                 */
                $this->sendJson([
                    'message' => 'Toner unmapped successfully',
                ]);
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson([
                    'message' => 'Unhandled Exception',
                    'uid'     => \Tangent\Logger\Logger::getUniqueId(),
                ]);
            }
        }
        else
        {
            $this->sendJsonError('Invalid Method');
        }
    }

    public function rentCalcAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $masterDeviceId = $this->getRequest()->getParam('id');
        $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $device = DeviceMapper::getInstance()->fetch(DeviceMapper::getInstance()->getWhereId([$masterDeviceId, $dealerId]));

        $services = \MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DistributorsForm::getServices($masterDeviceId);
        $dealerTonerVendors = [];
        foreach (\MPSToolbox\Legacy\Modules\Admin\Mappers\DealerTonerVendorMapper::getInstance()->fetchAllForDealer($dealerId) as $dtv) {
            $dealerTonerVendors[] = $dtv->manufacturerId;
        }
        $toners = [];
        foreach(TonerMapper::getInstance()->fetchTonersAssignedToDeviceForCurrentDealer($masterDeviceId) as $arr) {
            $manufacturerId = $arr['manufacturerId'];
            if (($manufacturerId!=$masterDevice->manufacturerId) && !in_array($manufacturerId, $dealerTonerVendors)) {
                continue;
            }
            if ($arr['tonerColorId']==1) $group='black'; else $group='color';
            $cpp = ''. ($arr['dealerCost'] / $arr['yield']);
            $toners[$manufacturerId][$group][$cpp] = $cpp;
        }
        $vendors=[];
        foreach ($toners as $manufacturerId=>$byMfg) {
            $manufacturer = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper::getInstance()->find($manufacturerId);
            $result=['black'=>0, 'color'=>0];
            foreach ($byMfg as $group=>$byGroup) {
                ksort($byGroup);
                $result[$group] = current($byGroup);
            }
            if ($result['color']) {
                $result['color'] = ($result['color']*3) + $result['black'];
            }
            $vendors[] = ['name'=>$manufacturer->fullname, 'value'=>implode(',',$result)];
        }

        $this->sendJson([
            'hardware'=>$device->cost,
            'service'=>empty($services)?0:$services[0]['price'],
            'pages'=>$device->pagesPerMonth,
            'vendor'=>$vendors
        ]);
    }

}