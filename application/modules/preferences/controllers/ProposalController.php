<?php
class Preferences_ProposalController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer                   = new Admin_Model_Dealer();
        $reportSettingFormService = new Preferences_Service_ReportSettings($dealer->getReportSettings(Zend_Auth::getInstance()->getIdentity()->dealerId));
        $form                     = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_helper->flashMessenger(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_helper->flashMessenger(array('danger' => 'Error saving report savings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    /**
     * This is where the admin can edit the system proposal preferences
     */
    public function systemAction ()
    {
        // Initialize and get the form
        $reportSettingFormService = new Preferences_Service_ReportSettings();
        $form                     = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_helper->flashMessenger(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_helper->flashMessenger(array('danger' => 'Error saving report savings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    public function userAction ()
    {
        // Initialize and get the form

        $user                     = new Application_Model_User();
        $reportSettingFormService = new Preferences_Service_ReportSettings($user->getReportSettings(Zend_Auth::getInstance()->getIdentity()->id));

        $form = $reportSettingFormService->getFormWithDefaults();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_helper->flashMessenger(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_helper->flashMessenger(array('danger' => 'Error saving report savings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }
}