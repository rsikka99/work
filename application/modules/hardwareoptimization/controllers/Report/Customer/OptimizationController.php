<?php
class Hardwareoptimization_Report_Customer_OptimizationController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction()
    {
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FINISHED);
        $this->initReportList();
        $this->view->headScript()->prependFile($this->view->baseUrl("/js/htmlReport.js"));

        $this->view->availableReports['CustomerOptimization']['active'] = true;
        $this->view->hardwareOptimization = $this->_hardwareOptimization;
        $this->view->optimization          = $this->getOptimizationViewModel();
    }
}