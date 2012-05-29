<?php

/**
 * InfoController:
 * This controller is responsible for routing users to requested actions
 * found on the footer of the layout (Contact Us, Terms & Conditions, Help).
 *
 * @author	Kevin Jervis
 */
class Proposalgen_InfoController extends Zend_Controller_Action
{

    function init ()
    {
        $config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app = $config->app;
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->view->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
    } // end function init
    
    function preDispatch ()
    {
    
    } // end function preDispatch
    
    public function contactusAction ()
    {
    } // end action contactusAction
    
    /**
     * Display the company End User License Agreement (EULA)
     *
     * @author Kevin Jervis
     * @version 1.0
     *         
     */
    public function termsandconditionsAction ()
    {
        $this->view->EulaPath = APPLICATION_PATH . "/../docs/EULA/officeDepotEULA.txt";
    
    } // end function termsandconditionsAction
    
    public function helpAction ()
    {
    } // end action helpAction
    
    public function aboutAction ()
    {
    } // end action aboutAction

} //end class infoController

