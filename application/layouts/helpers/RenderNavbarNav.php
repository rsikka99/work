<?php

/**
 * App_View_Helper_RenderNavbarNav
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_RenderNavbarNav extends Zend_View_Helper_Abstract
{
    /**
     * @param Zend_Navigation|Zend_Navigation_Page|Zend_Navigation_Page[] $container
     * @param string                                                      $navClass
     * @param int                                                         $maxDepth
     *
     * @return string
     */
    public function RenderNavbarNav ($container, $navClass = "nav navbar-nav", $maxDepth = 2)
    {
        $html = [];
        if (count($container) > 0)
        {
            $html[]  = sprintf('<ul class="%s">', $navClass);
            $html[]  = $this->_render_nav($container, $maxDepth);
            $html [] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Recursive function to render a nav
     *
     * @param Zend_Navigation_Page[] $pages
     * @param int                    $depth
     *
     * @return string
     */
    protected function _render_nav ($pages, $depth)
    {
        $html = [];
        foreach ($pages as $page)
        {
            if ($page->isVisible())
            {
                $dropdownContents = "";
                $hasDropDown      = false;
                if ($page->hasPages() && $depth > 1)
                {
                    $dropdownContents = $this->_render_nav($page, $depth - 1, $html);
                    $hasDropDown      = (strlen($dropdownContents) > 0);
                }

                $pageClasses = $page->getClass();

                if ($page->isActive())
                {
                    $pageClasses .= ' active';
                }

                if ($hasDropDown)
                {
                    $pageClasses .= ' dropdown';
                }

                if ($page->get('divider-above'))
                {
                    $html[] = '<li class="divider"></li>';
                }

                $html[] = sprintf('<li class="%s">', $pageClasses);

                $icon = ($page->get('icon')) ? sprintf('<i class="%s"></i> ', $page->get('icon')) : '';

                if ($hasDropDown)
                {
                    $html[] = sprintf('<a href="#" class="dropdown-toggle" data-toggle="dropdown">%s %s <b class="caret"></b></a>', $icon, $page->getLabel());
                }
                else
                {
                    $html[] = sprintf('<a href="%s">%s %s</a>', $page->getHref(), $icon, $page->getLabel());
                }

                if (strlen($dropdownContents) > 0)
                {
                    $html[] = '<ul class="dropdown-menu">';
                    $html[] = $dropdownContents;
                    $html[] = '</ul>';
                }


                $html[] = '</li>';

                if ($page->get('divider-below'))
                {
                    $html[] = '<li class="divider"></li>';
                }
            }
        }

        return implode(PHP_EOL, $html);
    }
}
