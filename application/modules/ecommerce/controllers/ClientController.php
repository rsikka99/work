<?php

use Tangent\Controller\Action;

class Ecommerce_ClientController extends Action
{
    public function indexAction() {
        $this->_pageTitle = ['E-commerce - Client Settings'];
        $this->view->clientId = $this->getRequest()->getParam('client');
        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
        if ($this->getRequest()->getMethod()=='POST') {
            $client = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->find($this->view->clientId);
            if ($client && ($client->dealerId == $dealerId)) {
                #--
                $client->priceLevel = $this->getRequest()->getParam('priceLevel');
                $client->transactionType = $this->getRequest()->getParam('transactionType');
                $client->notSupportedMasterDevices = implode(',',$this->getRequest()->getParam('notSupportedMasterDevices'));
                \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->save($client);
                #--
                $clientSettings = \MPSToolbox\Settings\Entities\ClientSettingsEntity::getClientSettings($client->id);
                $clientSettingsService = new \MPSToolbox\Settings\Service\ClientSettingsService();
                $newTonerRanks     = $this->getPostedRanks('proposedMonochromeRankSetArray');
                $currentTonerRanks = $clientSettings->proposedFleetSettings->getMonochromeRankSet()->getRankings();
                $clientSettingsService->saveTonerRankChanges($currentTonerRanks, $newTonerRanks, $clientSettings->proposedFleetSettings->getMonochromeRankSet()->id);

                $newTonerRanks     = $this->getPostedRanks('proposedColorRankSetArray');
                $currentTonerRanks = $clientSettings->proposedFleetSettings->getColorRankSet()->getRankings();
                $clientSettingsService->saveTonerRankChanges($currentTonerRanks, $newTonerRanks, $clientSettings->proposedFleetSettings->getColorRankSet()->id);
                #--
                $this->_flashMessenger->addMessage(["success" => "Your changes are saved"]);
                $this->redirect('/ecommerce/client?client='.$client->id);
            }
        }
        $this->view->clients = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->fetchAll(["dealerId=?"=>$dealerId]);
    }

    private function getPostedRanks($param)
    {
        $tonerRanks = [];

        $manufacturerIds = $this->getRequest()->getParam($param);
        if ($manufacturerIds)
        {
            $i = 0;
            foreach ($manufacturerIds as $manufacturerId)
            {
                $i++;
                $tonerRank                        = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel();
                $tonerRank->manufacturerId        = $manufacturerId;
                $tonerRank->rank                  = $i;
                $tonerRanks[(int)$manufacturerId] = $tonerRank;
            }
        }

        return $tonerRanks;
    }
}
