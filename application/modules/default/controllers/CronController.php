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
            $devices = $service->update($client['clientId'], $client['rmsUri'], $client['deviceGroup']);
            $service->checkDevices($devices, $client);
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

