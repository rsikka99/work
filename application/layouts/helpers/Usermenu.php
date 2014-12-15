<?php

/**
 * App_View_Helper_Usermenu
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Usermenu extends Zend_View_Helper_Abstract
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
            $html = $this->view->RenderNavbarNav($container, 'dropdown-menu', 1);

            // Bring back it's original visibility
            if ($wasInvisible)
            {
                $container->setVisible(false);
            }
        }

        return $html;
    }
}
