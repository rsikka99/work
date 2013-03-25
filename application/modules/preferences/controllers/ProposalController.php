<?php
class Preferences_ProposalController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer                   = new Admin_Model_Dealer();
        $reportSettingFormService = new Preferences_Service_ReportSetting($dealer->getReportSettings(Zend_Auth::getInstance()->getIdentity()->dealerId));
        $form                     = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report savings. Please correct the highlighted errors blow.'));
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
        $reportSettingFormService = new Preferences_Service_ReportSetting();
        $form                     = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report savings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    public function userAction ()
    {
        // Initialize and get the form
        $dealer                   = new Admin_Model_Dealer();
        $dealerReportSettings     = $dealer->getReportSettings(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $user                     = new Application_Model_User();
        $userReportSettings       = $user->getReportSettings(Zend_Auth::getInstance()->getIdentity()->id);
        $reportSettingFormService = new Preferences_Service_ReportSetting($userReportSettings);

        $form = $reportSettingFormService->getFormWithDefaults($dealerReportSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report savings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }
}