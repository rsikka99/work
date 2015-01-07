<?php

use MPSToolbox\Legacy\Entities\CountryEntity;
use MPSToolbox\Legacy\Repositories\CountryRepository;
use Tangent\Controller\Action;

/**
 * Class Api_CountryController
 *
 * This controller handles everything to do with creating/updating countries
 */
class Api_CountryController extends Action
{

    /**
     * Handles both the "all" and "find by" actions
     */
    public function indexAction ()
    {
        $countryId = $this->getParam('countryId', false);

        if ($countryId !== false)
        {
            $countryId = $this->getParam('countryId', false);

            $this->sendJson(CountryEntity::find($countryId)->toArray());
        }
        else
        {
            $searchTerm = $this->getParam('q', false);
            $pageLimit  = $this->getParam('page_limit', 10);
            $page       = $this->getParam('page', 1);

            $query = CountryRepository::getQuery();


            if (strlen($searchTerm) > 0)
            {
                $query->where('name', 'LIKE', "%$searchTerm%");
            }

            $count = $query->count();

            if ($page > 1)
            {
                $query->skip($pageLimit * ($page - 1));
            }

            $this->sendJson([
                'total'     => $count,
                'countries' => $query->limit($pageLimit)->get()->toArray(),
            ]);
        }
    }
}