<?php

/**
 * App_View_Helper_Footermenu
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Footermenu extends Zend_View_Helper_Abstract
{

    /**
     * @param string $class
     *
     * @return string
     */
    public function Footermenu ($class = 'nav nav-pills')
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages     = $this->view->MyNavigation()->getContainer();
        $container = $pages->findBy('id', 'footermenu');
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
                                   'ulClass'  => $class
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
