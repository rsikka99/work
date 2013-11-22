<?php
/**
 * Class Preferences_Memjet_OptimizationController
 */
class Preferences_Memjet_OptimizationController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);

        $settings                             = $dealer->getDealerSettings()->getMemjetOptimizationSettings();
        $memjetoptimizationSettingFormService = new Preferences_Service_Memjet_OptimizationSetting($settings);
        $form                                 = $memjetoptimizationSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
                $success = $memjetoptimizationSettingFormService->update($values);

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
        $memjetOptimizationSettingFormService = new Preferences_Service_Memjet_OptimizationSetting();
        $form                                 = $memjetOptimizationSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
                $success = $memjetOptimizationSettingFormService->update($values);

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
        $combinedDealerSettings = $dealer->getDealerSettings()->getMemjetoptimizationSettings()->toArray();

        // User
        $user                                 = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $memjetOptimizationSettingFormService = new Preferences_Service_Memjet_OptimizationSetting($user->getUserSettings()->getMemjetOptimizationSettings());

        $form = $memjetOptimizationSettingFormService->getFormWithDefaults($combinedDealerSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {

                $success = $memjetOptimizationSettingFormService->update($values);

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