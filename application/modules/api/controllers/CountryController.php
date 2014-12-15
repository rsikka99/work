<?php

use MPSToolbox\Legacy\Entities\CountryEntity;
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

            $query = CountryEntity::orderBy('name')->limit($pageLimit);


            if (strlen($searchTerm) > 0)
            {
                $query->where('name', 'LIKE', "%$searchTerm%");
            }

            $count = $query->count();

            if ($page > 1)
            {
                $query->offset($pageLimit * $page);
            }

            $this->sendJson([
                'total'     => $count,
                'countries' => $query->get()->toArray(),
            ]);
        }
    }
}