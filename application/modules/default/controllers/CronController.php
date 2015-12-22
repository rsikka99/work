<?php

use \MPSToolbox\Entities\RmsUpdateEntity;
use \MPSToolbox\Entities\TonerColorEntity;

class Default_CronController extends \Tangent\Controller\Action {

    public function rmsUpdateAction() {
        set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();


        $service = new \MPSToolbox\Services\RmsUpdateService();

        $clients = $service->getRmsClients();
        foreach ($clients as $client) {
            if (empty($client['deviceGroup'])) continue;
            echo "updating client: {$client['clientId']}<br>\n";
            $devices = $service->update($client['clientId'], new \MPSToolbox\Api\PrintFleet($client['rmsUri']), $client['deviceGroup']);
            $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings($client['dealerId']);
            $service->checkDevices($devices, $client, $settings->shopSettings);
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
    }

}

