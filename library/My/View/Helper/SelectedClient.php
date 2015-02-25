<?php

use MPSToolbox\Legacy\Entities\ClientEntity;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\UserViewedClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel;
use MPSToolbox\Legacy\Repositories\ClientRepository;

class My_View_Helper_SelectedClient extends Zend_View_Helper_Abstract
{

    /**
     * @var ClientEntity
     */
    protected $selectedClient;

    /**
     * Returns application settings
     */
    public function SelectedClient ()
    {
        if (!isset($this->selectedClient))
        {
            if ($this->view->MpsSession()->selectedClientId > 0)
            {
                $client = ClientRepository::find($this->view->MpsSession()->selectedClientId);
                if ($client instanceof ClientEntity && (int)$client->dealerId === (int)$this->view->Identity()->dealerId)
                {
                    $this->selectedClient = $client;

                    // Show that we've looked at the client recently
                    $userViewedClient = UserViewedClientMapper::getInstance()->find([$this->view->Identity()->id, $client->id]);
                    if ($userViewedClient instanceof UserViewedClientModel)
                    {
                        $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                        UserViewedClientMapper::getInstance()->save($userViewedClient);
                    }
                    else
                    {
                        $userViewedClient             = new UserViewedClientModel();
                        $userViewedClient->clientId   = $client->id;
                        $userViewedClient->userId     = $this->view->Identity()->id;
                        $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                        UserViewedClientMapper::getInstance()->insert($userViewedClient);
                    }
                }
            }
        }

        return $this->selectedClient;
    }
}