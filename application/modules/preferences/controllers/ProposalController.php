<?php
class Preferences_ProposalController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */}

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $defaultSettings = array_merge(Admin_Model_Mapper_Dealer::getInstance()->find($dealerId)->getReportSetting()->toArray());
        $reportSettingFormService = new Preferences_Service_ReportSettings($defaultSettings);
        $form = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if($request->isPost())
        {
            $values = $request->getPost();
            $form->isValid($values);
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
        $form = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if($request->isPost())
        {
            $values = $request->getPost();
            $form->isValid($values);
        }

        $this->view->form = $form;
    }

    public function userAction ()
    {
        // Initialize and get the form
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $defaultSettings = array_merge(Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchUserReportSetting($userId)->toArray(), Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchUserSurveySetting($userId)->toArray());
        $reportSettingFormService = new Preferences_Service_ReportSettings($defaultSettings);

        $form = $reportSettingFormService->getFormWithDefaults();

        $request = $this->getRequest();

        if($request->isPost())
        {
            $values = $request->getPost();
            $form->isValid($values);
            $success = $reportSettingFormService->update($values);
        }

        $this->view->form = $form;
    }
}