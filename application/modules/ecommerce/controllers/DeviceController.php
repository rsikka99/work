<?php

use Tangent\Controller\Action;

class Ecommerce_DeviceController extends Action
{
    public function indexAction() {
        $this->_pageTitle = ['E-commerce - Device Settings'];

        $clientId = $this->getRequest()->getParam('client');
        if ($clientId) {
            $client = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->find($clientId);
            if ($client) {
                $this->getMpsSession()->selectedClientId = $clientId;
            }
        }

        $this->view->clientId = $this->getMpsSession()->selectedClientId;
        $this->view->devices = [];

        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();

        if ($this->getRequest()->getMethod()=='POST') {
            $this->_flashMessenger->addMessage(["success" => "Your changes are saved"]);
            $this->redirect('/ecommerce/device');
        }

        $this->view->clients = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->fetchAll(["dealerId=?"=>$dealerId]);
    }

    public function detailsAction() {
        $id = $this->getRequest()->getParam('id');

        $db = Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare("select * from rms_device_instances where id=:id");
        $st->execute(['id'=>$id]);
        $this->view->device = $st->fetch();

        $this->_helper->layout()->disableLayout();
    }

    public function ajaxMapAction() {
        $instanceId = $this->getRequest()->getParam('instance');
        $masterDeviceId = $this->getRequest()->getParam('model');
        $masterDevice = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper::getInstance()->find($masterDeviceId);
        /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $instance */
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($instance && $masterDevice) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare('UPDATE rms_device_instances SET fullDeviceName=:fullDeviceName, masterDeviceId=:masterDeviceId WHERE masterDeviceId IS NULL AND manufacturer=:manufacturer AND modelName=:modelName');
            $st->execute(['fullDeviceName' => $masterDevice->getFullDeviceName(), 'masterDeviceId' => $masterDeviceId, 'modelName' => $instance->getModelName(), 'manufacturer' => $instance->getManufacturer()]);
        }
        $this->sendJson(['ok'=>true]);
    }

    public function ajaxTableAction() {
        $clientId = $this->getRequest()->getParam('client');
        $result=['data'=>[]];
        if ($clientId) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare("select * from rms_device_instances where clientId=:clientId");
            $st->execute(['clientId'=>$clientId]);
            foreach ($st->fetchAll() as $line) {
                if ($line['masterDeviceId']) {
                    $fullDeviceName =
'<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;
<a href="javascript:;" onclick="showModelModal('.$line['masterDeviceId'].')">'.$line['fullDeviceName'].'</a>';
                    $mapped = '<i class="fa fa-fw fa-check text-success"></i>';
                } else {
                    $fullDeviceName =
'<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;
<span class="text-danger">'.$line['fullDeviceName'].'</span>';
                    $mapped = '<a href="javascript:;" onclick="doMap('.$line['id'].', \''.htmlentities($line['fullDeviceName'], ENT_QUOTES).'\')" class="btn btn-default"><i class="fa fa-fw fa-chain"></i></a>';
                }

                $result['data'][] = [
                    'DT_RowId'=>'tr-'.$line['id'],
                    'model'=>$fullDeviceName,
                    'raw'=>$line['rawDeviceName'],
                    'mapped'=>$mapped,
                    'ipAddress'=>$line['ipAddress'],
                    'serialNumber'=>$line['serialNumber'],
                    'assetId'=>$line['assetId'],
                    'reportDate'=>$line['reportDate'],
                ];
            }
        }
        $this->sendJson($result);
    }
}
