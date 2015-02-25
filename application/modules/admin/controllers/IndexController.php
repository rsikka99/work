<?php
use Tangent\Controller\Action;

/**
 * Class Admin_IndexController
 */
class Admin_IndexController extends Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // action body
        $this->_pageTitle = ['Administration'];
    }
}

