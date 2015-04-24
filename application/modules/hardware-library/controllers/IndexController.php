<?php
use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_IndexController
 */
class HardwareLibrary_IndexController extends Action
{
    /**
     * This is the main landing page for preferences.
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Hardware Library'];
    }
}