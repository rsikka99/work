<?php
/**
 * Class Hardwareoptimization_DeviceswapsController
 */
class Hardwareoptimization_DeviceswapsController extends Tangent_Controller_Action
{
    public function indexAction ()
    {
        $form             = new Hardwareoptimization_Form_DeviceSwaps();
        $this->view->form = $form;
    }
}