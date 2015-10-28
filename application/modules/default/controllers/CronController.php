<?php

class Default_CronController extends \Tangent\Controller\Action {

    public function rmsUpdateAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();


        $service = new \MPSToolbox\Services\RmsUpdateService();

        $clients = $service->getRmsClients();
        foreach ($clients as $client) {
            $service->update($client['rmsUri'], $client['deviceGroup']);
        }
    }

}

