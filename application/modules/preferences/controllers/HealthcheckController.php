<?php
class Preferences_HealthcheckController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);

        $settings = $dealer->getDealerSettings()->getHealthcheckSettings()->toArray();

        $reportSettingFormService = new Preferences_Service_HealthcheckSetting($settings);
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
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
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
        $HealthcheckSettingFormService = new Preferences_Service_HealthcheckSetting();
        $form                     = $HealthcheckSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $HealthcheckSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    public function userAction ()
    {
        // Initialize and get the form

        // Dealer
        $dealer                 = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $combinedDealerSettings = $dealer->getDealerSettings()->getHealthcheckSettings()->toArray();

        // User
        $user                 = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $combinedUserSettings = $user->getUserSettings()->getHealthcheckSettings()->toArray();

        $reportSettingFormService = new Preferences_Service_ReportSetting($combinedUserSettings);

        $form = $reportSettingFormService->getFormWithDefaults($combinedDealerSettings);

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
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }
}