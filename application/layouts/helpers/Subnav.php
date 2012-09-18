<?php

/**
 * Application_View_Helper_Subnav
 *
 * @author Lee Robert
 *        
 */
class Application_View_Helper_Subnav extends Zend_View_Helper_Abstract
{

    public function Subnav ($navClass = "nav nav-pills", $maxDepth = 2)
    {
        $html = "";
        
        /* @var $pages Zend_Navigation */
        $pages = $this->view->navigation()->getContainer();
        $rootPage = $pages->getPages();
        
        $container = false;
        
        foreach ( $rootPage as $page )
        {
            if ($page->isActive(true))
            {
                $container = $page;
                break;
            }
        }
        
        // Only render if we have items to show
        if ($container && $container->hasPages())
        {
            $html = $this->view->navigation()
                ->menu()
                ->setUlClass($navClass)
                ->setMaxDepth($maxDepth)
                ->render($container);
        }
        
        return $html;
    }
}
