<?php
class Hardwareoptimization_Report_Dealer_OptimizationController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {

        $this->view->hardwareOptimization = $this->_hardwareOptimization;
        $this->view->optimization          = $this->getOptimizationViewModel();
    }
}