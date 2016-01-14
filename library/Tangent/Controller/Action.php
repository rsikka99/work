<?php
namespace Tangent\Controller;

use MPSToolbox\Legacy\Entities\ClientEntity;
use MPSToolbox\Legacy\Entities\RmsUploadEntity;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\UserViewedClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel;
use MPSToolbox\Legacy\Repositories\ClientRepository;
use MPSToolbox\Legacy\Repositories\RmsUploadRepository;
use stdClass;
use Zend_Auth;
use Zend_Db_Expr;
use Zend_Session;
use Zend_Session_Namespace;

class Action extends \Zend_Controller_Action
{
    /**
     * The page title. We use it in our postDispatch call
     *
     * @var string
     */
    protected $_pageTitle = '';

    /**
     * We always get an http request here
     *
     * @var \Zend_Controller_Request_Http
     */
    protected $_request = null;

    /**
     * We always return an http response
     *
     * @var \Zend_Controller_Response_Http
     */
    protected $_response = null;

    /**
     * @var \Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * View object
     *
     * @var \Tangent\View
     */
    public $view;

    /**
     * @var stdClass
     */
    protected $identity;

    /**
     * @var Zend_Session_Namespace
     */
    protected $mpsSession;

    /**
     * @var ClientEntity
     */
    protected $selectedClient;

    /**
     * @var RmsUploadEntity
     */
    protected $selectedRmsUpload;

    /**
     * @var \Zend_Mail_Transport_Abstract
     */
    protected static $mailTransport;

    /**
     * Helper Broker to assist in routing help requests to the proper object
     *
     * @var \Zend_Controller_Action_HelperBroker
     */
    protected $_helper = null;

    public function postDispatch ()
    {
        if (is_array($this->_pageTitle) && count($this->_pageTitle) > 0)
        {
            foreach ($this->_pageTitle as $title)
            {
                $this->view->headTitle($title);
            }

            $this->view->placeholder('page-header')->set(array_values($this->_pageTitle)[0]);
        }
        elseif (strlen($this->_pageTitle))
        {
            $this->view->headTitle($this->_pageTitle);
            $this->view->placeholder('page-header')->set($this->_pageTitle);
        }
        else
        {
            $this->view->headTitle('Default Title');
            $this->view->placeholder('page-header')->set('Default Title');
        }


        parent::postDispatch();
    }

    /**
     * Overridden Constructor.
     *
     * @see Zend_Controller_Action::__construct()
     *
     * @param \Zend_Controller_Request_Abstract  $request
     * @param \Zend_Controller_Response_Abstract $response
     * @param array                              $invokeArgs
     */
    public function __construct (\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = [])
    {
        $this->_flashMessenger = new \Zend_Controller_Action_Helper_FlashMessenger();
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Gets the identity of the currently logged in user
     *
     * @return stdClass
     */
    public function getIdentity ()
    {
        if (!isset($this->identity))
        {
            $this->identity = Zend_Auth::getInstance()->getIdentity();
        }

        return $this->identity;
    }

    /**
     * Checks to see if we're logged in
     *
     * @return bool
     */
    public function isLoggedIn ()
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    /**
     * Gets the identity of the currently logged in user
     *
     * @return Zend_Session_Namespace
     */
    public function getMpsSession ()
    {
        if (!isset($this->mpsSession))
        {
            $this->mpsSession = $this->view->MpsSession();
        }

        return $this->mpsSession;
    }

    /**
     * Gets the currently selected client
     *
     * @return ClientEntity
     */
    public function getSelectedClient ()
    {
        if (!isset($this->selectedClient))
        {
            if ($this->getMpsSession()->selectedClientId > 0)
            {
                $this->selectedClient = $this->view->SelectedClient();
            }
        }

        return $this->selectedClient;
    }

    /**
     * @param $client \MPSToolbox\Legacy\Entities\ClientEntity
     */
    public function setSelectedClient($client) {
        $this->selectedClient = $client;
        if ($client instanceof ClientEntity) {
            $this->getMpsSession()->selectedClientId = $client->id;
        } else {
            $this->getMpsSession()->selectedClientId = null;
        }
    }

    /**
     * Gets the currently selected upload
     *
     * @return RmsUploadEntity
     */
    public function getSelectedUpload ()
    {
        if (!isset($this->selectedRmsUpload))
        {
            if ($this->getMpsSession()->selectedRmsUploadId > 0)
            {
                $this->selectedRmsUpload = $this->view->SelectedRmsUpload();
            }
        }

        return $this->selectedRmsUpload;
    }

    /**
     * Return the Request object
     *
     * @return \Zend_Controller_Request_Http
     */
    public function getRequest ()
    {
        return $this->_request;
    }

    /**
     * Return the Response object
     *
     * @return \Zend_Controller_Response_Http
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
        try
        {
            if ($encodeData)
            {
                $data = $this->safe_json_encode($data);
            }

            $this->_helper->json($data, $sendNow, $keepLayouts, false);
        }
        catch (\Exception $e)
        {

        }
    }

    public function outputJson(array $array) {
        $data = $this->safe_json_encode($array);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $this->getResponse()->setBody($data);
    }

    protected function  safe_json_encode ($value)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0)
        {
            $encoded = json_encode($value, JSON_PRETTY_PRINT);
        }
        else
        {
            $encoded = json_encode($value);
        }
        switch (json_last_error())
        {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw new \Exception('Maximum stack depth exceeded');
            case JSON_ERROR_STATE_MISMATCH:
                throw new \Exception('Underflow or the modes mismatch');
            case JSON_ERROR_CTRL_CHAR:
                throw new \Exception('Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw new \Exception('Syntax error, malformed JSON');
            case JSON_ERROR_UTF8:
                $clean = $this->utf8ize($value);

                return $this->safe_json_encode($clean);
            default:
                throw new \Exception('Unknown error while encoding json');
        }
    }


    protected function utf8ize ($mixed)
    {
        if (is_array($mixed))
        {
            foreach ($mixed as $key => $value)
            {
                $mixed[$key] = $this->utf8ize($value);
            }
        }
        else if (is_string($mixed))
        {
            return utf8_encode($mixed);
        }

        return $mixed;
    }

    /**
     * Used to send json error to the client.
     *
     * @param string $message
     * @param bool   $sendNow
     * @param bool   $keepLayouts
     */
    public function sendJsonError ($message, $sendNow = true, $keepLayouts = false)
    {
        $this->getResponse()->setHttpResponseCode(500);
        $this->_helper->json(["error" => $message], $sendNow, $keepLayouts);
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
     */
    public function redirector ($action, $controller = null, $module = null, array $params = [])
    {
        $this->_helper->redirector($action, $controller, $module, $params);
    }

    /**
     * Used to redirect to a route name
     *
     * @see Zend_Controller_Action_Helper_Redirector::gotoRoute()
     *
     * @param string $routeName
     * @param array  $urlOptions
     * @param bool   $reset
     * @param bool   $encode
     */
    public function redirectToRoute ($routeName, $urlOptions = [], $reset = false, $encode = true)
    {
        $this->_helper->redirector->gotoRoute($urlOptions, $routeName, $reset, $encode);
    }

    /**
     * Uses the redirector function to navigation to the next step
     *
     * @param \My_Navigation_Abstract $navigation
     * @param null|array              $params
     */
    public function gotoNextNavigationStep (\My_Navigation_Abstract $navigation, $params = null)
    {
        $activeStep = $navigation->activeStep;
        if ($activeStep instanceof \My_Navigation_Step)
        {
            // Only redirect when there is a step to redirect to.
            if ($activeStep->nextStep instanceof \My_Navigation_Step)
            {
                $this->redirectToRoute($activeStep->nextStep->route, [$params]);
            }
        }
    }

    /**
     * Uses the redirector function to navigation to the previous step
     *
     * @param \My_Navigation_Abstract $navigation
     * @param null|array              $params
     */
    public function gotoPreviousNavigationStep (\My_Navigation_Abstract $navigation, $params = null)
    {
        $activeStep = $navigation->activeStep;
        if ($activeStep instanceof \My_Navigation_Step)
        {
            // Only redirect when there is a step to redirect to.
            if ($activeStep->previousStep instanceof \My_Navigation_Step)
            {
                $this->redirectToRoute($activeStep->previousStep->route, [$params]);
            }
        }
    }

    /**
     * Gets the layout
     *
     * @return \Zend_Layout
     */
    public function getLayout ()
    {
        return $this->_helper->layout();
    }

    /**
     * @return \Zend_Mail_Transport_Abstract
     * @deprecated
     */
    public static function getMailTransport()
    {
        return null;
        /**
        if (null === self::$mailTransport) {
            $config = \Zend_Registry::get('config');
            $email  = $config->email;

            //grab the email configuration settings from application.ini
            $emailConfig = [
                'auth'     => 'login',
                'username' => $email->username,
                'password' => $email->password,
                'ssl'      => $email->ssl,
                'port'     => $email->port,
                'host'     => $email->host
            ];

            //grab the email host from application.ini
            self::$mailTransport = new \Zend_Mail_Transport_Smtp($emailConfig['host'], $emailConfig);
        }
        return self::$mailTransport;
        **/
    }

    /**
     * @param \Zend_Mail_Transport_Abstract $mailTransport
     */
    public static function setMailTransport($mailTransport)
    {
        self::$mailTransport = $mailTransport;
    }

    /**
     * @return \Zend_Controller_Action_Helper_FlashMessenger
     */
    public function getFlashMessenger()
    {
        return $this->_flashMessenger;
    }

    /**
     * @param \Zend_Controller_Action_Helper_FlashMessenger $flashMessenger
     */
    public function setFlashMessenger($flashMessenger)
    {
        $this->_flashMessenger = $flashMessenger;
    }




}