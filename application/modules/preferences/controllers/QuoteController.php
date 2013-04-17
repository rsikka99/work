<?php
class Preferences_QuoteController extends Tangent_Controller_Action
{
    public function indexAction () { /**Do Nothing*/ }

    public function systemAction ()
    {
        $quoteService = new Preferences_Service_QuoteSetting();
        $form         = $quoteService->getForm();
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $quoteService->update($values);

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
        // Dealer
        $dealer                 = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $combinedDealerSettings = $dealer->getDealerSettings()->getQuoteSettings();

        // User
        $user                 = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $combinedUserSettings = $user->getUserSettings()->getQuoteSettings()->toArray();
        $quoteSettingFormService = new Preferences_Service_QuoteSetting($combinedUserSettings);

        $form = $quoteSettingFormService->getFormWithDefaults($combinedDealerSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $quoteSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Quote settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving quote settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);

        $settings = $dealer->getDealerSettings()->getQuoteSettings()->toArray();

        $quoteSettingFormService = new Preferences_Service_QuoteSetting($settings);
        $form                     = $quoteSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $quoteSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Quote settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving quote settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }
}