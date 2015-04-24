<?php

/**
 * App_View_Helper_Adminmenu
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Adminmenu extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function Adminmenu ()
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages     = $this->view->MyNavigation()->getContainer();
        $container = $pages->findBy('id', 'adminmenu');
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
                               ->renderMenu($container, [
                                   'minDepth' => 0,
                                   'maxDepth' => 1,
                                   'ulClass'  => 'dropdown-menu',
                               ]);

            // Bring back it's original visibility
            if ($wasInvisible)
            {
                $container->setVisible(false);
            }
        }

        return $html;
    }
}
