<?php

class Quotegen_Quote_DebugController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::DEBUG_CONTROLLER);
    }

    public function indexAction ()
    {
        $deviceService = new Quotegen_Service_BuildConfiguration();
        
        $results = $deviceService->getAllAvailableDevices();
        
        echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
        var_dump($results);
        
        
        $results = $deviceService->getAllFavoriteDevicesForUser($this->_userId);
        echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
        var_dump($results);
        die();
    }
}

