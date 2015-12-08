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
        $clientId = $this->getRequest()->getParam('client');
        $reverse = $this->getRequest()->getParam('reverse');
        $instanceId = $this->getRequest()->getParam('instance');
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($clientId && $reverse && $instance) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare('UPDATE rms_device_instances SET masterDeviceId=null WHERE clientId=:clientId and manufacturer=:manufacturer AND modelName=:modelName');
            $st->execute(['clientId' => $clientId, 'modelName' => $instance->getModelName(), 'manufacturer' => $instance->getManufacturer()]);
        } else {
            $masterDeviceId = $this->getRequest()->getParam('model');
            $masterDevice = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper::getInstance()->find($masterDeviceId);
            /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $instance */
            if ($instance && $masterDevice) {
                $db = Zend_Db_Table::getDefaultAdapter();
                $st = $db->prepare('UPDATE rms_device_instances SET fullDeviceName=:fullDeviceName, masterDeviceId=:masterDeviceId WHERE masterDeviceId IS NULL AND manufacturer=:manufacturer AND modelName=:modelName');
                $st->execute(['fullDeviceName' => $masterDevice->getFullDeviceName(), 'masterDeviceId' => $masterDeviceId, 'modelName' => $instance->getModelName(), 'manufacturer' => $instance->getManufacturer()]);
            }
        }
        $this->sendJson(['ok'=>true]);
    }
    public function ajaxIgnoreAction() {
        $clientId = $this->getRequest()->getParam('client');
        $instanceId = $this->getRequest()->getParam('instance');
        $reverse = $this->getRequest()->getParam('reverse');
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($instance) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare('UPDATE rms_device_instances SET `ignore`='.($reverse?'0':'1').' where clientId=:clientId and masterDeviceId IS NULL AND manufacturer=:manufacturer AND modelName=:modelName');
            $st->execute(['clientId'=>$clientId, 'modelName'=>$instance->getModelName(), 'manufacturer'=>$instance->getManufacturer()]);
        }
        $this->sendJson(['ok'=>true]);
    }

    public function ajaxTodoAction() {
        $this->view->RenderNavbarNav();
        $client = App_View_Helper_RenderNavbarNav::getIncompleteClientSettings();
        $dealer = App_View_Helper_RenderNavbarNav::getIncompleteDealerSettings();
        $device = App_View_Helper_RenderNavbarNav::getIncompleteDevices();

        $this->sendJson([
            ['item'=>'client', 'n'=>$client],
            ['item'=>'dealer', 'n'=>$dealer],
            ['item'=>'device', 'n'=>$device],
            ['item'=>'all', 'n'=>$client+$dealer+$device],
        ]);

    }

    public function ajaxTableAction() {
        $clientId = $this->getRequest()->getParam('client');
        $result=['data'=>[]];
        if ($clientId) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare("select * from rms_device_instances where clientId=:clientId");
            $st->execute(['clientId'=>$clientId]);

            $s = new \MPSToolbox\Services\RmsDeviceInstanceService();
            $incomplete = $s->getIncomplete($clientId);

            $staleDate = date('Y-m-d', strtotime('-7 DAY'));

            foreach ($st->fetchAll() as $line) {
                if ($line['ignore']) {
                    $icon = '<span style="display:none">4 '.$line['fullDeviceName'].'</span><a href="javascript:;" onclick="if (window.confirm(\'Unignore this model?\')) unignore('.$line['id'].')"><i class="fa fa-fw fa-remove" style="color:black" title="Ignored, click to unignore"></i><a>';
                    $fullDeviceName = '<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;'.$line['fullDeviceName'];
                } else if ($line['masterDeviceId']) {
                    $color='';
                    $icon = '<span style="display:none">3 '.$line['fullDeviceName'].'</span><a href="javascript:;" onclick="if (window.confirm(\'Unmap this model?\')) unmap('.$line['id'].')"><i class="fa fa-fw fa-check text-success" title="Click to unmap this model"></i></a>';
                    if (isset($incomplete[$line['id']])) {
                        $icon = '<span style="display:none">2 '.$line['fullDeviceName'].'</span><a href="javascript:;" onclick="if (window.confirm(\'Unmap this model?\')) unmap('.$line['id'].')"><i class="fa fa-fw fa-bars text-danger" title="Unavailable Supplies, click to unmap this model"></i></a>';
                        $color='text-danger';
                    } else if ($line['reportDate']<$staleDate) {
                        $icon = '<span style="display:none">3 '.$line['fullDeviceName'].'</span><a href="javascript:;" onclick="if (window.confirm(\'Unmap this model?\')) unmap('.$line['id'].')"><i class="fa fa-fw fa-warning text-warning" title="Stale, click to unmap this model"></i></a>';
                    }
                    $fullDeviceName =
'<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;
<a href="javascript:;" onclick="showModelModal('.$line['masterDeviceId'].')" class="'.$color.'">'.$line['fullDeviceName'].'</a>';
                } else {
                    $fullDeviceName =
'<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;
<a href="javascript:;" class="text-danger" onclick="doMap('.$line['id'].', \''.htmlentities($line['fullDeviceName'], ENT_QUOTES).'\')">'.$line['fullDeviceName'].'</a>';
                    $icon = '<span style="display:none">1 '.$line['fullDeviceName'].'</span><a href="javascript:;" onclick="doMap('.$line['id'].', \''.htmlentities($line['fullDeviceName'], ENT_QUOTES).'\')"><i class="fa fa-fw fa-wrench text-danger" title="Unmapped, click to map this model"></i></a>';
                }

                $result['data'][] = [
                    'DT_RowId'=>'tr-'.$line['id'],
                    'model'=>$fullDeviceName,
                    'raw'=>$line['rawDeviceName'],
                    'icon'=>$icon,
                    'ipAddress'=>$line['ipAddress'],
                    'serialNumber'=>$line['serialNumber'],
                    'location'=>$line['location'],
                    'reportDate'=>$line['reportDate'],
                ];
            }
        }
        $this->sendJson($result);
    }
}
