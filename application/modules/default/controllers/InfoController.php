<?php

class Default_InfoController extends Zend_Controller_Action
{

    /**
     * Display the company Terms and Conditions
     */
    public function termsandconditionsAction ()
    {
        $file = APPLICATION_PATH . "/../data/info/termsandconditions.txt";
        $text = 'Not Available';
        
        if (file_exists($file))
        {
            $text = str_replace("’", "'", file_get_contents($file));
        }
        
        $this->view->text = $text;
    }

    /**
     * Display the company End User License Agreement (EULA)
     */
    public function eulaAction ()
    {
        $file = APPLICATION_PATH . "/../data/info/eula.txt";
        $text = 'Not Available';
        
        if (file_exists($file))
        {
            $text = str_replace("’", "'", file_get_contents($file));
        }
        
        $this->view->text = $text;
    }

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

