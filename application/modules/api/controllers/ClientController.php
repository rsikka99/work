<?php

use MPSToolbox\Legacy\Entities\ClientEntity;
use Tangent\Controller\Action;

/**
 * Class Api_ClientController
 *
 * This controller handles everything to do with creating/updating clients
 */
class Api_ClientController extends Action
{

    /**
     * Handles both the "all" and "find by" actions
     */
    public function indexAction ()
    {
        $clientId  = $this->getParam('clientId', false);
        $requester = trim($this->getParam('requester', ''));
        $dealerId  = $this->getIdentity()->dealerId;
        $userId    = $this->getIdentity()->id;

        if ($clientId !== false)
        {

            $clientId = $this->getParam('clientId', false);
            $client   = ClientEntity::where('dealerId', '=', $dealerId)->where('id', '=', $clientId)->first();
            if ($client instanceof ClientEntity)
            {
                $this->sendJson($client->toArray());
            }
            else
            {
                $this->getResponse()->setHttpResponseCode(404);
                $this->sendJson(['message' => 'A client with that id could not be found']);
            }
        }

        if (strcasecmp($requester, "dataTables") === 0)
        {
            $searchParams = $this->_getParam('search', null);

            /**
             * Build data adapter
             */
            $clientRepository = new \MPSToolbox\Legacy\Repositories\ClientRepository();
            $clientQuery      = $clientRepository->forDealerWithLastSeen($dealerId, $userId);
            $dataAdapter      = new \Tangent\Grid\DataAdapter\EloquentAdapter($clientQuery);

            /**
             * Setup request/response
             */
            $columnFactory = new \Tangent\Grid\Order\ColumnFactory(['accountNumber', 'companyName', 'employeeCount', 'legalName', 'dateViewed']);
            $gridRequest   = new \Tangent\Grid\Request\DataTableRequest($this->getAllParams(), $columnFactory);
            $gridResponse  = new \Tangent\Grid\Response\DataTableResponse($gridRequest);

            /**
             * Setup Filters
             */
            $filterCriteriaValidator = new Zend_Validate_InArray(array('haystack' => array('companyName')));

            if (is_array($searchParams) && array_key_exists('value', $searchParams) && strlen($searchParams['value']) > 0)
            {
                $dataAdapter->addFilter(new \Tangent\Grid\Filter\Contains('companyName', "%{$searchParams['value']}%"));
            }


            /**
             * Setup grid
             */
            $gridService = new \Tangent\Grid\Grid($gridRequest, $gridResponse, $dataAdapter);
            $this->sendJson($gridService->getGridResponseAsArray());
        }
        else
        {
            $searchTerm = $this->getParam('q', false);
            $pageLimit  = $this->getParam('page_limit', 10);
            $page       = $this->getParam('page', 1);

            $query = ClientEntity::orderBy('companyName')->limit($pageLimit);
            $query->where('dealerId', '=', $dealerId);


            if (strlen($searchTerm) > 0)
            {
                $query->where('name', 'LIKE', "%$searchTerm%");
            }

            $count = $query->count();

            if ($page > 1)
            {
                $query->offset($pageLimit * ($page - 1));
            }

            $this->sendJson([
                'total'   => $count,
                'clients' => $query->get()->toArray(),
            ]);
        }
    }
}