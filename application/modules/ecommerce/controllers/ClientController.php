<?php

use Tangent\Controller\Action;

class Ecommerce_ClientController extends Action
{
    public function indexAction() {
        $this->_pageTitle = ['E-commerce - Client Settings'];

        $service = new \MPSToolbox\Services\PriceLevelService();
        $db = Zend_Db_Table::getDefaultAdapter();

        $clientId = $this->getRequest()->getParam('client');
        if ($clientId) {
            $client = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->find($clientId);
            if ($client) {
                $this->getMpsSession()->selectedClientId = $clientId;
            }
        }

        $this->view->clientId = $this->getMpsSession()->selectedClientId;
        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
        if ($this->view->clientId && $this->getRequest()->getMethod()=='POST') {
            $client = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->find($this->view->clientId);
            if ($client && ($client->dealerId == $dealerId)) {
                #--
                $client->templateNum = $this->getRequest()->getParam('templateNum');
                $client->priceLevelId = $this->getRequest()->getParam('priceLevelId');
                $client->transactionType = $this->getRequest()->getParam('transactionType');
                $client->ecomMonochromeRank = implode(',',$this->getRequest()->getParam('ecomMonochromeRank'));
                $client->ecomColorRank = implode(',',$this->getRequest()->getParam('ecomColorRank'));
                \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->save($client);
                #--
                if (!empty($client->webId)) {
                    $g = new \GuzzleHttp\Client([]);
                    $g->get('http://proxy.mpstoolbox.com/shopify/sync_customer.php?id='.$client->id.'&dealerId='.$dealerId.'&origin='.$_SERVER['HTTP_HOST']);
                }
                #--
                $this->_flashMessenger->addMessage(["success" => "Your changes are saved"]);
                $this->redirect('/ecommerce/client');
                return;
            }
        }

        if ($this->view->clientId && isset($_GET['monitoringEnabled'])) {
            $client = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->find($this->view->clientId);
            $client->monitoringEnabled = intval($_GET['monitoringEnabled']);
            \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->save($client);
            $this->redirect('/ecommerce/client');
            return;
        }

        $this->view->clients = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->fetchAll(["dealerId=?"=>$dealerId]);

        $this->view->price_levels = $service->listByDealer($dealerId);
        if (empty($this->view->price_levels)) {
            $service->insert($dealerId, 'Base', 30, true);
            $this->view->price_levels = $service->listByDealer($dealerId);
        }
    }

}
