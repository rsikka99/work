<?php
use Tangent\Controller\Action;

/**
 * Class Preferences_IndexController
 */
class Preferences_IndexController extends Action
{
    /**
     * This is the main landing page for preferences.
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Settings'];
    }

}