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
        $this->sendJson(["message" => "index ok"]);
    }

    public function printauditPushAction() {
        $clientId = $this->getRequest()->getParam('clientId');
        if (!$clientId) {
            $this->sendJson(["message" => "error: clientId not provided"]);
        }
        if ($this->getRequest()->getMethod()=='GET') {
            echo '<form action="?" enctype="multipart/form-data" method="post"><input type="file" name="upload"><button>Submit</button></form>';
            exit();
        }
        if ($this->getRequest()->getMethod()=='POST') {

            $service = new \MPSToolbox\Services\RmsRealtimeService();

            $upload = current($_FILES);
            if (empty($upload) || empty($upload['tmp_name']) || !is_uploaded_file($upload['tmp_name']) || (filesize($upload['tmp_name'])==0)) {
                $this->sendJson(["message" => "error: file not received"]);
            }
            $xml = simplexml_load_file($upload['tmp_name']);
            $service->processPrintauditXml($clientId,$xml);
        }

        $this->sendJson(["message" => "printaudit ok"]);
    }

}