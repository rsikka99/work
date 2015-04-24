<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\ManufacturerEntity;
use Tangent\Controller\Action;

/**
 * Class Api_ManufacturersController
 *
 * This controller handles everything to do with creating/updating manufacturers
 */
class Api_ManufacturersController extends Action
{

    /**
     *
     */
    public function indexAction ()
    {
        $manufacturerIdParam = $this->getParam('manufacturerId', false);


        if ($manufacturerIdParam === false)
        {
            $manufacturers = ManufacturerEntity::all();
            $this->sendJson(['data' => $manufacturers->toArray()]);
        }
        else
        {
            $manufacturerIds = explode(',', $manufacturerIdParam);
            $manufacturers   = ManufacturerEntity::find($manufacturerIds);
            if ($manufacturers instanceof ManufacturerEntity)
            {
                $this->sendJson($manufacturers->toArray());
            }
            else if ($manufacturers instanceof \Illuminate\Support\Collection)
            {
                $this->sendJson(['data' => $manufacturers->toArray()]);
            }
            else
            {
                $this->sendJsonError("A manufacturer with that ID does not exist.");
            }
        }

    }

    /**
     * Handles creating a new master device
     */
    public function createAction ()
    {
        $this->sendJson(["message" => "This is action is not implemented yet."]);
    }

    /**
     *
     */
    public function deleteAction ()
    {
        $this->sendJson(["message" => "This is action is not implemented yet."]);
    }

    /**
     *
     */
    public function updateAction ()
    {
        $this->sendJson(["message" => "This is action is not implemented yet."]);
    }
}