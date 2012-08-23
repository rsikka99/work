<?php

/**
 * Quotegen_View_Helper_Quotemenu
 *
 * @author Lee Robert
 *        
 */
class Quotegen_View_Helper_Quotemenu extends Zend_View_Helper_Abstract
{
    const DEBUG_CONTROLLER = 'quote_debug';
    const DEVICES_CONTROLLER = 'quote_devices';
    const SETTINGS_CONTROLLER = 'quote_settings';
    const REPORTS_CONTROLLER = 'quote_reports';
    const GROUPS_CONTROLLER = 'quote_groups';
    static $pages = array (
            self::DEVICES_CONTROLLER => 'Build Devices', 
            self::SETTINGS_CONTROLLER => 'Adjust Settings', 
            self::REPORTS_CONTROLLER => 'View & Print Reports', 
            self::DEBUG_CONTROLLER => 'Debug Calculations',
            self::GROUPS_CONTROLLER => 'New grouping' 
    );
    static $activePage = 'quote_devices';

    static function setActivePage ($controller)
    {
        self::$activePage = $controller;
    }

    public function Quotemenu ()
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages = $this->view->navigation()->getContainer();
        $container = $pages->findBy('id', 'quotemenu');
        if ($container && $container->hasPages())
        {
            // If it's invisible, we'll need to turn it visible to be rendered properly
            $wasInvisible = false;
            if (! $container->isVisible())
            {
                $wasInvisible = true;
                $container->setVisible(true);
            }
            
            // Render the menu
            $html = $this->view->navigation()
                ->menu()
                ->renderMenu($container, array (
                    'minDepth' => 0, 
                    'maxDepth' => 0, 
                    'ulClass' => 'nav nav-tabs' 
            ));
            
            // Bring back it's original visibility
            if ($wasInvisible)
            {
                $container->setVisible(false);
            }
        }
        
        return $html;
    }
}
