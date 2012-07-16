<?php

/**
 * InfoController:
 * This controller is responsible for routing users to requested actions
 * found on the footer of the layout (About, Terms & Conditions, Eula).
 *
 * @author John Sadler
 */
class Default_InfoController extends Zend_Controller_Action
{

    function init ()
    {
    } // end function init

    
    function preDispatch ()
    {
    } // end function preDispatch
    
    /**
     * Display the company Terms and Conditions
     *
     */
    public function termsandconditionsAction ()
    {
        $this->view->path = APPLICATION_PATH . "/../data/info/termsandconditions.txt";
    } // end function termsandconditionsAction

    /**
     * Display the company End User License Agreement (EULA)
     */
    public function eulaAction ()
    {
        $this->view->path = APPLICATION_PATH . "/../data/info/eula.txt";
    } // end function eulaAction

    /**
     * Displays the program verion and meta information
     */
    public function aboutAction ()
    {
        try
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $result = $db->fetchRow("SELECT * FROM database_metadata WHERE meta_key='dbversion' LIMIT 1;");
        }
        catch ( Exception $e )
        {
            // Do nothing, database version is not available at this point
            $result = FALSE;
        }
        
        $this->view->databaseVersion = "Not Available";
        if ($result)
        {
            $this->view->databaseVersion = $result ["meta_value"];
        }
    } // end action aboutAction


} //end class infoController

