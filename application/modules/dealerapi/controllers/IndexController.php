<?php
use Tangent\Controller\Action;

/**
 * Class Api_IndexController
 */
class Dealerapi_IndexController extends Action
{
    public function __construct (\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = []) {
        parent::__construct($request, $response, $invokeArgs);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }

}