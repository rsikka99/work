<?php

/**
 * App_View_Helper_Subnav
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Subnav extends Zend_View_Helper_Abstract
{
    /**
     * @param string $navClass
     * @param int    $maxDepth
     *
     * @return string
     */
    public function Subnav ($navClass = "nav navbar-nav", $maxDepth = 2)
    {
        $html = '';

        /* @var $pages Zend_Navigation */
        $pages = $this->view->MyNavigation()->getContainer();

        /* @var $rootPage Zend_Navigation_Page[] */
        $rootPage = $pages->getPages();

        $container = false;

        foreach ($rootPage as $page)
        {
            if ($page->isActive(true))
            {
                foreach ($page as $subPage)
                {
                    if ($subPage->isActive(true))
                    {
                        $container = $subPage;
                        break;
                    }
                }
                break;
            }
        }

        // Only render if we have items to show
        if ($container && $container->hasPages())
        {
            $html = $this->view->MyNavigation()
                               ->menu()
                               ->setUlClass($navClass)
                               ->setMaxDepth($maxDepth)
                               ->render($container);

            if (stripos($html, '<li') === false)
            {
                $html = '';
            }
        }

        return $html;
    }
}
