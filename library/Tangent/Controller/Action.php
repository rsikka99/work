<?php
class Tangent_Controller_Action extends Zend_Controller_Action
{
    /**
     * We always get an http request here
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request = null;

    /**
     * We always return an http response
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response = null;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * View object
     *
     * @var Zend_View
     */
    public $view;

    /**
     * Helper Broker to assist in routing help requests to the proper object
     *
     * @var Zend_Controller_Action_HelperBroker
     */
    protected $_helper = null;

    /**
     * Overridden Constructor.
     *
     * @see Zend_Controller_Action::__construct()
     *
     * @param Zend_Controller_Request_Abstract  $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array                             $invokeArgs
     */
    public function __construct (Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);

        $this->_flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
    }


    /**
     * Return the Request object
     *
     * @return Zend_Controller_Request_Http
     */
    public function getRequest ()
    {
        return $this->_request;
    }

    /**
     * Return the Response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse ()
    {
        return $this->_response;
    }

    /**
     * Used to send json to the client.
     *
     * @see Zend_Controller_Action_Helper_Json::direct()
     *
     * @param      $data
     * @param bool $sendNow
     * @param bool $keepLayouts
     * @param bool $encodeData
     */
    public function sendJson ($data, $sendNow = true, $keepLayouts = false, $encodeData = true)
    {
        $this->_helper->json($data, $sendNow, $keepLayouts, $encodeData);
    }

    /**
     * Used to redirect a client to an action
     *
     * @see Zend_Controller_Action_Helper_Redirector::direct()
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     *
     * @return void
     */
    public function redirector ($action, $controller = null, $module = null, array $params = array())
    {
        $this->_helper->redirector($action, $controller, $module, $params);
    }
}