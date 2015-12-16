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
            $st = $db->prepare('UPDATE rms_device_instances SET `ignore`='.($reverse?'0':'1').' where clientId=:clientId and manufacturer=:manufacturer AND modelName=:modelName');
            $arr = ['clientId'=>$clientId, 'modelName'=>$instance->getModelName(), 'manufacturer'=>$instance->getManufacturer()];
            $st->execute($arr);
        }
        $this->sendJson(['ok'=>true]);
    }
    public function ajaxIgnoreSingleAction() {
        $clientId = $this->getRequest()->getParam('client');
        $instanceId = $this->getRequest()->getParam('instance');
        $reverse = $this->getRequest()->getParam('reverse');
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($instance) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare('UPDATE rms_device_instances SET `ignore`='.($reverse?'0':'1').' where clientId=:clientId and id=:id');
            $st->execute(['clientId'=>$clientId, 'id'=>$instanceId]);
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
                    $fullDeviceName = $line['fullDeviceName'];
                    $icon = '<span style="display:none">4 '.$line['fullDeviceName'].'</span><i class="fa fa-fw fa-remove context" style="color:black" title="Ignored" data-target="#context-menu-'.$line['id'].'"></i>
                    <ul class="dropdown-menu ul-context-menu" role="menu" id="context-menu-'.$line['id'].'" style="display:none">
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Unignore this model?\')) unignore('.$line['id'].')">Unignore this device model</a></li>
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Unignore this single device?\')) unignoreSingle('.$line['id'].')">Unignore this single device</a></li>
                    </ul>';
                } else if ($line['masterDeviceId']) {
                    $color='';
                    $li = '
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Unmap this model?\')) unmap('.$line['id'].')">Unmap this device model</a></li>
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Ignore this model?\')) ignore('.$line['id'].')">Ignore this device model</a></li>
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Ignore this single device?\')) ignoreSingle('.$line['id'].')">Ignore this single device</a></li>
                    ';
                    $icon = '<span style="display:none">3 '.$line['fullDeviceName'].'</span><i class="fa fa-fw fa-check text-success context" data-target="#context-menu-'.$line['id'].'"></i>';
                    if (isset($incomplete[$line['id']])) {
                        $li = '<li><a href="javascript:;" onclick="showModelModal('.$line['masterDeviceId'].')">Edit device model details</a></li>'.$li;
                        $icon = '<span style="display:none">2 '.$line['fullDeviceName'].'</span><i class="fa fa-fw fa-bars text-danger context" data-target="#context-menu-'.$line['id'].'" title="Unavailable Supplies"></i>';
                        $color='text-danger';
                    } else if ($line['reportDate']<$staleDate) {
                        $icon = '<span style="display:none">3 '.$line['fullDeviceName'].'</span><i class="fa fa-fw fa-warning text-warning context" data-target="#context-menu-'.$line['id'].'" title="Stale"></i>';
                    }
                    $fullDeviceName = '<a href="javascript:;" onclick="showModelModal('.$line['masterDeviceId'].')" class="'.$color.'">'.$line['fullDeviceName'].'</a>';
                    $icon.='
                    <ul class="dropdown-menu ul-context-menu" role="menu" id="context-menu-'.$line['id'].'" style="display:none">'.$li.'</ul>';

                } else {
                    $icon = '<span style="display:none">1 '.$line['fullDeviceName'].'</span><i class="fa fa-fw fa-wrench text-danger context" data-target="#context-menu-'.$line['id'].'" title="Unmapped"></i>';
                    $fullDeviceName = '<a href="javascript:;" class="text-danger" onclick="doMap('.$line['id'].', \''.htmlentities($line['fullDeviceName'], ENT_QUOTES).'\')">'.$line['fullDeviceName'].'</a>';
                    $icon.='
                    <ul class="dropdown-menu ul-context-menu" role="menu" id="context-menu-'.$line['id'].'" style="display:none">
                        <li><a href="javascript:;" onclick="doMap('.$line['id'].', \''.htmlentities($line['fullDeviceName'], ENT_QUOTES).'\')">Map this device model</a></li>
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Ignore this model?\')) ignore('.$line['id'].')">Ignore this device model</a></li>
                        <li><a href="javascript:;" onclick="if (window.confirm(\'Ignore this single device?\')) ignoreSingle('.$line['id'].')">Ignore this single device</a></li>
                    </ul>';
                }

                $fullDeviceName = '<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;' . $fullDeviceName;

                $result['data'][] = [
                    'DT_RowId'=>'tr-'.$line['id'],
                    'icon'=>$icon,
                    'model'=>$fullDeviceName,
                    'raw'=>$line['rawDeviceName'],
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
