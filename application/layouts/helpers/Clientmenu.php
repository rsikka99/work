<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * App_View_Helper_Clientmenu
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_Clientmenu extends Zend_View_Helper_Abstract
{

    /**
     * @param ClientModel $client
     *
     * @return string
     */
    public function Clientmenu ($client)
    {
        // Get the container
        $html = "";
        /* @var $pages Zend_Navigation */
        $pages = $this->view->MyNavigation()->getContainer();

        if ($client)
        {
            $container = $pages->findBy('id', 'clientmenu');
        }
        else
        {
            $container = $pages->findBy('id', 'noclientmenu');
        }
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
