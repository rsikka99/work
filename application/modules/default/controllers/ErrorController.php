<?php
use Tangent\Controller\Action;

/**
 * Class Default_ErrorController
 */
class Default_ErrorController extends Action
{

    public function init ()
    {
    }

    public function errorAction ()
    {
        $this->_pageTitle = ['Error'];
        $errors           = $this->_getParam('error_handler');


        $forwardToAction = 'four-oh-four';
        switch ($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                // 404 error -- controller or action not found
                $this->logAndPrepareExceptions($errors, false, Zend_Log::INFO, $errors->type);
                break;
            default :
                switch ($errors->exception->getCode())
                {
                    case 403 :
                        // Access Denied!
                        $forwardToAction = 'not-authorized';
                        $this->logAndPrepareExceptions($errors, false, Zend_Log::INFO, $errors->type);
                        break;
                    case 404 :
                        // Access Denied!
                        $forwardToAction = 'four-oh-four';
                        $this->logAndPrepareExceptions($errors, false, Zend_Log::INFO, $errors->type);
                        break;
                    default :
                        // Application Error
                        $forwardToAction = 'application-error';
                        $this->logAndPrepareExceptions($errors, true, Zend_Log::CRIT, $errors->type);
                        break;
                }
                break;
        }

        $this->forward($forwardToAction);

    }

    /**
     * Gets the appropriate Zend_Log facility, or false if none are registered.
     *
     * @return boolean Zend_Log
     */
    public function getLog ()
    {
        if (!Zend_Registry::isRegistered("Zend_Log"))
        {
            return false;
        }

        return Zend_Registry::get("Zend_Log");
    }

    /**
     * Logs the error and prepares the view with the appropriate information
     *
     * @param stdClass $errors
     * @param bool     $log
     * @param int      $priority
     * @param int      $code
     */
    public function logAndPrepareExceptions ($errors, $log = true, $priority, $code)
    {
        /*
         * Generate a uid just in case two exceptions happen at the exact same time on different threads and we end up
         * getting mixed lines of a different exception
         */
        $uid             = uniqid();
        $this->view->uid = $uid;
        $exceptions      = [];

        /* @var $ex \Exception */
        $ex = $errors->exception;

        if ($log)
        {
            // Declare the start of the trace
            \Tangent\Logger\Logger::crit("[$uid] - --------Started Trace [ $code ]--------.");
        }
        // Loop through all the exceptions, and log them
        do
        {
            // Log exception, if logger available
            if ($log)
            {
                \Tangent\Logger\Logger::log(sprintf('[%1$s] - Exception [%6$s] "%2$s" occurred in %3$s on line %4$s: %5$s', $uid, $ex->getCode(), $ex->getFile(), $ex->getLine(), $ex->getMessage(), get_class($ex)), $priority);
                \Tangent\Logger\Logger::log(sprintf('[%1$s] - Stack Trace:\n%2$s', $uid, $ex->getTraceAsString()), $priority);
            }

            $exceptions [] = $ex;
        } while (!is_null($ex = $ex->getPrevious()));

        if ($log)
        {
            // Include request parameters afterwards
            \Tangent\Logger\Logger::log('Request Parameters', $priority, null, $errors->request->getParams());
            \Tangent\Logger\Logger::crit("[$uid] - --------Finished Trace--------.");
        }

        // Conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true)
        {
            $this->view->exceptions = $this->getFormattedExceptions($exceptions);
            //$this->_helper->viewRenderer('error');
        }

        $this->view->request = $errors->request;
    }

    /**
     * Formats exceptions for viewing
     *
     * @param Exception[] $exceptions
     *
     * @return stdClass[]
     */
    public function getFormattedExceptions ($exceptions)
    {
        $exceptionList = [];
        foreach ($exceptions as $exception)
        {
            $exceptionModel          = new stdClass();
            $exceptionModel->message = $exception->getMessage();

            $stackTrace = $exception->getTraceAsString();
            $stackTrace = preg_replace("/" . str_replace("/", "\/", APPLICATION_BASE_PATH) . "/", "", $stackTrace);
            $stackTrace = preg_replace("/(#\d+\s*)(?!\/vendor|\/public)(\/.+)(\/[a-zA-Z]*\.php\(\d+\))/", "$1$2<strong style='font-size: 1.2em'>$3</strong>", $stackTrace);

            $exceptionModel->stackTrace = $stackTrace;

            $exceptionList[] = $exceptionModel;
        }

        return $exceptionList;
    }

    /**
     * Handles application errors
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function applicationErrorAction ()
    {
        $this->getResponse()->setHttpResponseCode(500);

        if ($this->getRequest()->isXmlHttpRequest())
        {
            $response = [
                'message' => 'An application error occurred.',
                'uid'     => $this->view->uid,
            ];

            if (isset($this->view->exceptions) && APPLICATION_ENV != 'production')
            {
                $response['exceptions'] = $this->view->exceptions;
                $response['request']    = $this->view->request->getParams();
            }

            $this->sendJson($response);
        }
    }

    /**
     * Handles access denied
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function notAuthorizedAction ()
    {
        $this->getResponse()->setHttpResponseCode(403);
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $response = [
                'message' => '403. Not Authorized.',
            ];

            if (isset($this->view->exceptions) && APPLICATION_ENV != 'production')
            {
                $response['exceptions'] = $this->view->exceptions;
                $response['request']    = $this->view->request->getParams();
            }

            $this->sendJson($response);
        }
    }

    /**
     * Handles invalid urls
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function fourOhFourAction ()
    {
        $this->getResponse()->setHttpResponseCode(404);
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $response = [
                'message' => '404 Page Not Found.',
            ];

            if (APPLICATION_ENV != 'production')
            {
                if (isset($this->view->exceptions))
                {
                    $response['exceptions'] = $this->view->exceptions;
                }
                $response['request'] = $this->view->request->getParams();
            }

            $this->sendJson($response);
        }
    }
}

