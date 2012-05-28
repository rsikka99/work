<?php

/**
 * ErrorController - The default error controller class
 *
 * @author	Chris Garrah
 */

class Proposalgen_ErrorController extends Zend_Controller_Action
{

    /**
     * Initialize objects to be used throughout the Error controller.
     */
    function init ()
    {
        // Initilize the view object
        $this->config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app = $this->config->app;
        
        // $this->logger = Zend_Registry::get('logger');
    } // End function init()

    
    public function getLog ()
    {
        if (! Zend_Registry::isRegistered("Zend_Log"))
        {
            return false;
        }
        return Zend_Registry::get("Zend_Log");
    }

    /**
     * error Action handles exceptions thrown throughout the application.
     * (when not running in development mode)
     * - Application errors
     * - Errors in the controller chain arising from missing
     * controller classes and/or action methods
     */
    public function errorAction ()
    {
        // Disable the default layout
        $this->_helper->layout->disableLayout();
        $this->view->title = "Error - Proposal Generator";
        $this->view->userFriendlyMessage = 'Application Error.';
        $errors = $this->_getParam('error_handler');
        switch ($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                // 404 error -- controller or action not found
                $this->view->title = "404 Not Found - Proposal Generator";
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->userFriendlyMessage = '404 Page Not Found';
                break;
            
            default :
                // Application Error
                // Display the error without changing the reponse header
                if ($this->getInvokeArg('displayExceptions') == true)
                {
                    $this->_helper->viewRenderer('programmerError');
                    $i = 1;
                    $currentException = $errors->exception;
                    $this->view->exceptions = array ();
                    $this->view->exceptions [] = $currentException;
                    $this->view->errorMessage .= "#$i = " . get_class($currentException) . ": " . str_replace(APPLICATION_PATH, "", $currentException->getFile()) . " @ Line " . $currentException->getLine() . ":\n" . $currentException->getMessage();
                    while ( ! is_null($currentException = $currentException->getPrevious()) )
                    {
                        $this->view->exceptions [] = $currentException;
                    }
                
                }
                // Log exception, if logger available
                if (FALSE !== ($log = $this->getLog()))
                {
                    /*
                     * Generate a uid just in case two exceptions happen at the exact same time on different threads 
                     * and we end up getting mixed lines of a different exception
                     */
                    $uid = uniqid();
                    
                    $ex = $errors->exception;
                    $log->crit("[$uid] - An Error Occured: Stack Trace will follow:");
                    $log->crit("[$uid] - Exception code '" . $ex->getCode() . "' occured in " . $ex->getFile() . " on line " . $ex->getLine() . ": " . $ex->getMessage());
                    $log->crit("[$uid] - Stack Trace:\n" . $ex->getTraceAsString());
                    
                    while ( ! is_null($ex = $ex->getPrevious()) )
                    {
                        $log->crit("[$uid] - Exception code '" . $ex->getCode() . "' occured in " . $ex->getFile() . " on line " . $ex->getLine() . ": " . $ex->getMessage());
                        $log->crit("[$uid] - Stack Trace:\n" . $ex->getTraceAsString());
                    }
                    $log->crit("[$uid] - Stack Trace finished.");
                }
                break;
        
        } // end switch
    

    } // End function errorAction()

    
    public function accessdeniedAction ()
    {
        // Disable the default layout
        $this->_helper->layout->disableLayout();
        $this->view->title = "Access Denied - Proposal Generator";
    
    } // End function accessdeniedAction()
} // end error controller

