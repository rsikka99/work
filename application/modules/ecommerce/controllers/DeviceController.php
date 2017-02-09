<?php

use Tangent\Controller\Action;

class Ecommerce_DeviceController extends Action
{
    public function is_root() {
        return \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId() == 1;
    }

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
        /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $instance */
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($this->is_root()) $clientId = $instance->getClient()->getId();
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
        /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $instance */
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($this->is_root()) $clientId = $instance->getClient()->getId();
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
        /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $instance */
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($this->is_root()) $clientId = $instance->getClient()->getId();
        if ($instance) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare('UPDATE rms_device_instances SET `ignore`='.($reverse?'0':'1').' where clientId=:clientId and id=:id');
            $st->execute(['clientId'=>$clientId, 'id'=>$instanceId]);
        }
        $this->sendJson(['ok'=>true]);
    }

    public function ajaxHideAction() {
        $clientId = $this->getRequest()->getParam('client');
        $instanceId = $this->getRequest()->getParam('instance');
        $reverse = $this->getRequest()->getParam('reverse');
        /** @var \MPSToolbox\Entities\RmsDeviceInstanceEntity $instance */
        $instance = \MPSToolbox\Entities\RmsDeviceInstanceEntity::find($instanceId);
        if ($this->is_root()) $clientId = $instance->getClient()->getId();
        if ($instance) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->prepare('UPDATE rms_device_instances SET `hidden`='.($reverse?'0':'1').' where clientId=:clientId and id=:id');
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

    public function templateSelectedAction() {
        $id = $this->getParam('id');
        $value = $this->getParam('value');
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query('update rms_device_instances set email_template='.intval($value).' where id='.intval($id));
        $this->sendJson([]);
    }

    public function ajaxTableAction() {
        $clientId = $this->getRequest()->getParam('client');
        $result=['data'=>[]];

        $arr = [];
        $incomplete = [];

        $db = Zend_Db_Table::getDefaultAdapter();
        if ($clientId ) {
            $st = $db->prepare("SELECT * FROM rms_device_instances WHERE clientId=:clientId");
            $st->execute(['clientId' => $clientId]);
            $arr = $st->fetchAll();

            $s = new \MPSToolbox\Services\RmsDeviceInstanceService();
            $incomplete = $s->getIncomplete($clientId);

        } else if ($this->is_root()) {
            $st = $db->prepare("
SELECT *
FROM rms_device_instances
where `ignore`=0 and clientId in (select id from clients where dealerId in (select dealerId from dealer_settings where shopSettingsId in (select id from shop_settings where shopifyName<>''))) and (
  masterDeviceId is null or
  masterDeviceId not in (
    select master_device_id
    from device_toners dt
      join master_devices msub on dt.master_device_id=msub.id
      join toners t on dt.toner_id=t.id and t.manufacturerId = msub.manufacturerId
  )
)
");
            $st->execute(['clientId' => $clientId]);
            $arr = $st->fetchAll();
            $incomplete=array();
            foreach ($arr as $line) $incomplete[$line['id']] = $line['id'];
        }

        $staleDate = date('Y-m-d', strtotime('-7 DAY'));

        foreach ($arr as $line) {
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

            $hidden = '<a href="javascript:;" title="Hide" onclick="hide('.$line['id'].')"><i class="fa fa-fw fa-eye"></i></a>';
            if ($line['hidden']) $hidden = '<a href="javascript:;" title="Unhide" onclick="unhide('.$line['id'].')" style="color:red"><i class="fa fa-fw fa-eye-slash"></i></a>';

            $fullDeviceName = '<a href="javascript:;" onclick="showDetailsModal('.$line['id'].')"><i class="fa fa-fw fa-info-circle text-info"></i></a>&nbsp;' . $fullDeviceName;

            $template='<span id="template-'.$line['id'].'" data-value="'.$line['email_template'].'"><span onclick="selectTemplate('.$line['id'].')" style="cursor:pointer">'.($line['email_template']?$line['email_template']:'Client').'</span></span>';

            $result['data'][] = [
                'DT_RowId'=>'tr-'.$line['id'],
                'icon'=>$icon,
                'hidden'=>$hidden,
                'model'=>$fullDeviceName,
                'raw'=>$line['rawDeviceName'],
                'ipAddress'=>$line['ipAddress'],
                'serialNumber'=>$line['serialNumber'],
                'location'=>$line['location'],
                'reportDate'=>$line['reportDate'],
                'template'=>$template,
            ];
        }
        $this->sendJson($result);
    }
}
