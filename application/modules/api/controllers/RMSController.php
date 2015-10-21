<?php

use Tangent\Controller\Action;

/**
 * Class Api_RMSController
 *
 * This controller handles everything to do with creating/updating manufacturers
 */
class Api_RMSController extends Action
{

    /**
     *
     */
    public function indexAction ()
    {
    }

    /**
     * Handles creating a new master device
     */
    public function toShopifyAction ()
    {
        $this->sendJson(["message" => "ok"]);
    }

}