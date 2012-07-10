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
    const PRICING_CONTROLLER = 'quote_pricing';
    const GROUPING_CONTROLLER = 'quote_grouping';
    const PAGES_CONTROLLER = 'quote_pages';
    const SETTINGS_CONTROLLER = 'quote_settings';
    const REPORTS_CONTROLLER = 'quote_reports';
    static $pages = array (
            self::DEVICES_CONTROLLER => 'Build Devices', 
            self::PRICING_CONTROLLER => 'Configure Pricing',
            self::GROUPING_CONTROLLER => 'Group Devices',
            self::PAGES_CONTROLLER => 'Manage Pages',
            self::SETTINGS_CONTROLLER => 'Adjust Settings', 
            self::REPORTS_CONTROLLER => 'View & Print Reports' 
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
