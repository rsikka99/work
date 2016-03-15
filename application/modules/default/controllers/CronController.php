<?php

use \MPSToolbox\Entities\RmsUpdateEntity;
use \MPSToolbox\Entities\TonerColorEntity;

class Default_CronController extends \Tangent\Controller\Action {

    public function rmsUpdateAction() {
        set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $onlyClientId = $this->getParam('client');

        $service = new \MPSToolbox\Services\RmsUpdateService();

        $clients = $service->getRmsClients();

        foreach ($clients as $client) {
            if (empty($client['deviceGroup'])) continue;
            if ($onlyClientId && ($onlyClientId!=$client['clientId'])) continue;
            if (!$onlyClientId && ($client['monitoringEnabled']==0)) continue;

            echo "updating client: {$client['clientId']}<br>\n";
            if (preg_match('#^\w+-\w+-\w+-\w+-\w+$#', $client['deviceGroup'])) {
                $devices = $service->update($client['clientId'], new \MPSToolbox\Api\PrintFleet($client['rmsUri']), $client['deviceGroup']);
                $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings($client['dealerId']);
                $service->checkDevices($devices, $client, $settings->shopSettings);
            }
        }
    }

    public function distributorsAction() {
        set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $service = new \MPSToolbox\Services\DistributorUpdateService();

        $dealerSuppliers = $service->getDealerSuppliers();
        foreach ($dealerSuppliers as $dealerSupplier) {
            $service->updatePrices($dealerSupplier);
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $arr = $db->query("
SELECT dealers.id FROM dealers join `dealer_settings` on dealers.id=dealer_settings.dealerId join shop_settings on dealer_settings.`shopSettingsId`=shop_settings.id where shop_settings.shopifyName<>''
        ")->fetchAll();
        foreach ($arr as $line) {
            file_get_contents('http://proxy.mpstoolbox.com/shopify/dist_update.php?dealerId='.$line['id'].'&origin='.$_SERVER['HTTP_HOST']);
        }
    }

}

