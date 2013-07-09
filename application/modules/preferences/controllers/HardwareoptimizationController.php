<?php
/**
 * Class Preferences_HardwareoptimizationController
 */
class Preferences_HardwareoptimizationController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);

        $settings                               = $dealer->getDealerSettings()->getHardwareOptimizationSettings();
        $hardwareoptimizationSettingFormService = new Preferences_Service_HardwareoptimizationSetting($settings);
        $form                                   = $hardwareoptimizationSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
                $success = $hardwareoptimizationSettingFormService->update($values);

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
        // Initialize and get the form
        $HardwareoptimizationSettingFormService = new Preferences_Service_HardwareoptimizationSetting();
        $form                                   = $HardwareoptimizationSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
                $success = $HardwareoptimizationSettingFormService->update($values);

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
        // Dealer
        $dealer                 = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $combinedDealerSettings = $dealer->getDealerSettings()->getHardwareoptimizationSettings()->toArray();

        // User
        $user                                   = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $HardwareoptimizationSettingFormService = new Preferences_Service_HardwareoptimizationSetting($user->getUserSettings()->getHardwareOptimizationSettings());

        $form = $HardwareoptimizationSettingFormService->getFormWithDefaults($combinedDealerSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {

                $success = $HardwareoptimizationSettingFormService->update($values);

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