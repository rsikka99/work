<?php

/**
 * Application_View_Helper_Usermenu
 *
 * @author Lee Robert
 *
 */
class Application_View_Helper_Usermenu extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function Usermenu ()
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages     = $this->view->MyNavigation()->getContainer();
        $container = $pages->findBy('id', 'usermenu');
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
                    'ulClass'  => 'dropdown-menu'
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
