<?php

/**
 * Class Admin_IndexController
 */
class Admin_IndexController extends Tangent_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // action body
        $this->view->headTitle('Administration');
    }
}

