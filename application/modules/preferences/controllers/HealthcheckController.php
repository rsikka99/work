<?php

/**
 * Class Preferences_HealthcheckController
 */
class Preferences_HealthcheckController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        $this->view->headTitle('Settings');
        $this->view->headTitle('Dealer Health Check');
        // Initialize and get the form
        $dealer                        = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $healthcheckSettingFormService = new Preferences_Service_HealthcheckSetting($dealer->getDealerSettings()->getHealthcheckSettings());
        $form                          = $healthcheckSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
                $success = $healthcheckSettingFormService->update($values);

                if ($success)
                {
                    $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
                }
            }
            else
            {
                $this->redirector('index', 'index', 'admin');
            }
        }

        $this->view->form = $form;
    }

    /**
     * This is where the admin can edit the system proposal preferences
     */
    public function systemAction ()
    {
        $this->view->headTitle('Settings');
        $this->view->headTitle('System Health Check');
        // Initialize and get the form
        $HealthcheckSettingFormService = new Preferences_Service_HealthcheckSetting();
        $form                          = $HealthcheckSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
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
            else
            {
                $this->redirector('index', 'index', 'admin');
            }

        }

        $this->view->form = $form;
    }

    public function userAction ()
    {
        $this->view->headTitle('Settings');
        $this->view->headTitle('User Health Check');
        // Dealer
        $dealer                 = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $combinedDealerSettings = $dealer->getDealerSettings()->getHealthcheckSettings()->toArray();
        // User
        $user                          = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $healthcheckSettingFormService = new Preferences_Service_HealthcheckSetting($user->getUserSettings()->getHealthcheckSettings());

        $form = $healthcheckSettingFormService->getFormWithDefaults($combinedDealerSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
                $success = $healthcheckSettingFormService->update($values);

                if ($success)
                {
                    $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
                }
            }
            else
            {
                $this->redirector('index', 'index', 'admin');
            }
        }

        $this->view->form = $form;
    }
}