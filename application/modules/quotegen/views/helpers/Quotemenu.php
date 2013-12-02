<?php

/**
 * Quotegen_View_Helper_Quotemenu
 *
 * @author Lee Robert
 *
 */
class Quotegen_View_Helper_Quotemenu extends Zend_View_Helper_Abstract
{
    const SETTINGS_CONTROLLER      = 'quote_settings';
    const DEVICES_CONTROLLER       = 'quote_devices';
    const GROUPS_CONTROLLER        = 'quote_groups';
    const PAGES_CONTROLLER         = 'quote_pages';
    const PROFITABILITY_CONTROLLER = 'quote_profitability';
    const REPORTS_CONTROLLER       = 'quote_reports';
    const DEBUG_CONTROLLER         = 'quote_debug';
    static $pages = array(
        self::SETTINGS_CONTROLLER      => 'Adjust Settings',
        self::DEVICES_CONTROLLER       => 'Build Devices',
        self::GROUPS_CONTROLLER        => 'Group Devices',
        self::PAGES_CONTROLLER         => 'Add Pages',
        self::PROFITABILITY_CONTROLLER => 'Profitability',
        self::REPORTS_CONTROLLER       => 'Reports',
        self::DEBUG_CONTROLLER         => 'Debug'
    );
    static $activePage = 'quote_settings';

    /**
     * @param string $controller The controller that is active
     */
    static function setActivePage ($controller)
    {
        self::$activePage = $controller;
    }

    /**
     * @return string
     */
    public function Quotemenu ()
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages     = $this->view->MyNavigation()->getContainer();
        $container = $pages->findBy('id', 'quotemenu');
        if ($container && $container->hasPages())
        {
            // If it's invisible, we'll need to turn it visible to be rendered properly
            $wasInvisible = false;
            if (!$container->isVisible())
            {
                $wasInvisible = true;
                $container->setVisible(true);
            }

            // Render the menu
            $html = $this->view->MyNavigation()
                ->menu()
                ->renderMenu($container, array(
                                              'minDepth' => 0,
                                              'maxDepth' => 0,
                                              'ulClass'  => 'nav nav-pills'
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
