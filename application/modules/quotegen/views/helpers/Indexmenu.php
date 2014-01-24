<?php

/**
 * Class Quotegen_View_Helper_Indexmenu
 */
class Quotegen_View_Helper_Indexmenu extends Zend_View_Helper_Abstract
{
    /**
     * @return string
     */
    public function Indexmenu ()
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages     = $this->view->navigation()->getContainer();
        $container = $pages->findBy('id', 'indexmenu');
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
