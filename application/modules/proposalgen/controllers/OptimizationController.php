<?php
class Proposalgen_OptimizationController extends Proposalgen_Library_Controller_Proposal
{
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_OPTIMIZATION);
        // Get all devices

        // Every time we save anything related to a report, we should save it (updates the modification date)
        $this->saveReport();
        $form = new Proposalgen_Form_DeviceSwapChoice($this->_report);

        // Get all devices
        $devices = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchDevicesInstancesForMapping($this->_report->id, 'reportId', 'asc');
        $this->view->devices = $devices;

           // Every time we save anything related to a report, we should save it (updates the modification date)
//        $this->saveReport();
        // Call the base controller to send us to the next logical step in the proposal.
//        $this->gotoNextStep();

        $this->view->title = 'Hardware Optimization';
    }
}