<?php

/**
 * Quotegen_View_Helper_Quotemenu
 *
 * @author Lee Robert
 *        
 */
class Quotegen_View_Helper_Quotemenu extends Zend_View_Helper_Abstract
{
    const DEVICES_CONTROLLER = 'quote_devices';
    const PAGES_CONTROLLER = 'quote_pages';
    const SETTINGS_CONTROLLER = 'quote_settings';
    const REPORTS_CONTROLLER = 'quote_reports';
    static $pages = array (
            self::DEVICES_CONTROLLER => 'Devices', 
            self::PAGES_CONTROLLER => 'Pages', 
            self::SETTINGS_CONTROLLER => 'Settings', 
            self::REPORTS_CONTROLLER => 'Reports' 
    );
    static $activePage = 'quote_devices';

    static function setActivePage ($controller)
    {
        self::$activePage = $controller;
    }

    public function Quotemenu ()
    {
        if (! self::$activePage)
        {
            self::$activePage = self::DEVICES_CONTROLLER;
        }
        $this->view->quoteMenuPages = self::$pages;
        $this->view->activeQuoteMenuPage = self::$activePage;
        return $this->view->render('_partials/Quotemenu.phtml');
    }
}
