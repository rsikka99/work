<?php

class Default_ErrorController extends Zend_Controller_Action
{

    public function errorAction ()
    {
        $errors = $this->_getParam('error_handler');
        
        if (! $errors || ! $errors instanceof ArrayObject)
        {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        $showProgrammerError = false;
        
        switch ($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = '404 - Page not found';
                $this->_helper->viewRenderer('404');
                break;
            default :
                switch ($errors->exception->getCode())
                {
                    case 403 :
                        // Access Denied!
                        $this->getResponse()->setHttpResponseCode(403);
                        $this->_helper->viewRenderer('403');
                        break;
                    default :
                        // Application Error
                        $this->getResponse()->setHttpResponseCode(500);
                        $priority = Zend_Log::CRIT;
                        $this->view->message = 'Application error';
                        $this->_helper->viewRenderer('500');
                        $showProgrammerError = true;
                        break;
                }
                break;
        }
        
        /*
         * Generate a uid just in case two exceptions happen at the exact same time on different threads and we end up
         * getting mixed lines of a different exception
         */
        $uid = uniqid();
        $this->view->uid = $uid;
        $exceptions = array ();
        $priority = Zend_Log::CRIT;
        
        $ex = $errors->exception;
        
        // Declare the start of the trace
        My_Log::crit("[$uid] - --------Started Trace--------.");
        
        // Loop through all the exceptions, and log them
        do
        {
            // Log exception, if logger available
            My_Log::log("[$uid] - Exception '" . $ex->getCode() . "' occured in " . $ex->getFile() . " on line " . $ex->getLine() . ": " . $ex->getMessage(), $priority);
            My_Log::log("[$uid] - Stack Trace:\n" . $ex->getTraceAsString(), $priority);
            $exceptions [] = $ex;
        }
        while ( ! is_null($ex = $ex->getPrevious()) );
        
        // Include request parameters afterwards
        My_Log::log('Request Parameters', $priority, null, $errors->request->getParams());
        My_Log::crit("[$uid] - --------Finished Trace--------.");
        
        // Conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true && $showProgrammerError)
        {
            $this->view->exceptions = $exceptions;
            $this->_helper->viewRenderer('error');
        }
        
        $this->view->request = $errors->request;
    }

    /**
     * Gets the appropriate Zend_Log facility, or false if none are registered.
     *
     * @return boolean Zend_Log
     */
    public function getLog ()
    {
        if (! Zend_Registry::isRegistered("Zend_Log"))
        {
            return false;
        }
        return Zend_Registry::get("Zend_Log");
    }
}

