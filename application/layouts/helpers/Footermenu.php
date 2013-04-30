<?php

/**
 * Application_View_Helper_Footermenu
 *
 * @author Lee Robert
 *
 */
class Application_View_Helper_Footermenu extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function Footermenu ()
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages     = $this->view->navigation()->getContainer();
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
            $html = $this->view->navigation()
                ->menu()
                ->renderMenu($container, array(
                                              'minDepth' => 0,
                                              'maxDepth' => 0,
                                              'ulClass'  => 'footer-nav'
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
