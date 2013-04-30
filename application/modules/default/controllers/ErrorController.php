<?php

/**
 * Class Default_ErrorController
 */
class Default_ErrorController extends Tangent_Controller_Action
{

    public function init ()
    {
    }

    public function errorAction ()
    {
        $errors = $this->_getParam('error_handler');

        $forwardToAction = 'four-oh-four';
        switch ($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                // 404 error -- controller or action not found
                break;
            default :
                switch ($errors->exception->getCode())
                {
                    case 403 :
                        // Access Denied!
                        $forwardToAction = 'not-authorized';
                        break;
                    case 404 :
                        // Access Denied!
                        $forwardToAction = 'four-oh-four';
                        break;
                    default :
                        // Application Error
                        $forwardToAction = 'application-error';
                        $this->logAndPrepareExceptions($errors, Zend_Log::CRIT, $errors->type);
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
     * @param int      $priority
     * @param int      $code
     */
    public function logAndPrepareExceptions ($errors, $priority, $code)
    {
        /*
         * Generate a uid just in case two exceptions happen at the exact same time on different threads and we end up
         * getting mixed lines of a different exception
         */
        $uid             = uniqid();
        $this->view->uid = $uid;
        $exceptions      = array();

        /* @var $ex \Exception */
        $ex = $errors->exception;

        // Declare the start of the trace
        Tangent_Log::crit("[$uid] - --------Started Trace [ $code ]--------.");

        // Loop through all the exceptions, and log them
        do
        {
            // Log exception, if logger available
            Tangent_Log::log("[$uid] - Exception '" . $ex->getCode() . "' occurred in " . $ex->getFile() . " on line " . $ex->getLine() . ": " . $ex->getMessage(), $priority);
            Tangent_Log::log("[$uid] - Stack Trace:\n" . $ex->getTraceAsString(), $priority);
            $exceptions [] = $ex;
        } while (!is_null($ex = $ex->getPrevious()));

        // Include request parameters afterwards
        Tangent_Log::log('Request Parameters', $priority, null, $errors->request->getParams());
        Tangent_Log::crit("[$uid] - --------Finished Trace--------.");

        // Conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true)
        {
            $this->view->exceptions = $exceptions;
            //$this->_helper->viewRenderer('error');
        }

        $this->view->request = $errors->request;
    }

    public function applicationErrorAction ()
    {
        $this->getResponse()->setHttpResponseCode(500);
    }

    public function notAuthorizedAction ()
    {
        $this->getResponse()->setHttpResponseCode(403);
    }

    public function fourOhFourAction ()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }
}

