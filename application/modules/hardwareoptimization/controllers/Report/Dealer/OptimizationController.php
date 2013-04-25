<?php
class Hardwareoptimization_Report_Dealer_OptimizationController extends Hardwareoptimization_Library_Controller_Action
{
    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FINISHED);

        $this->initHtmlReport();
        $this->initReportList();
        $this->view->availableReports['DealerOptimization']['active'] = true;

//        $this->view->headScript()->prependFile($this->view->baseUrl("/js/htmlReport.js"));

        $this->view->hardwareOptimization                                     = $this->_hardwareOptimization;
        $this->view->optimization                                             = $this->getOptimizationViewModel();
    }
}