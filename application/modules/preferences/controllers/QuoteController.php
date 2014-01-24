<?php

/**
 * Class Preferences_QuoteController
 */
class Preferences_QuoteController extends Tangent_Controller_Action
{
    public function indexAction () { /**Do Nothing*/ }

    public function systemAction ()
    {
        $this->view->headTitle('Settings');
        $this->view->headTitle('System Hardware Quote');
        $quoteService = new Preferences_Service_QuoteSetting();
        $form         = $quoteService->getForm();
        $request      = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
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
        $this->view->headTitle('User Hardware Quote');
        // Dealer
        $dealer                 = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $combinedDealerSettings = $dealer->getDealerSettings()->getQuoteSettings();

        // User
        $user                    = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteSettingFormService = new Preferences_Service_QuoteSetting($user->getUserSettings()->getQuoteSettings());
        $form                    = $quoteSettingFormService->getFormWithDefaults($combinedDealerSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
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
            else
            {
                $this->redirector('index', 'index', 'admin');
            }
        }

        $this->view->form = $form;
    }

    public function dealerAction ()
    {
        $this->view->headTitle('Settings');
        $this->view->headTitle('Dealer Hardware Quote');
        // Initialize and get the form
        $dealer = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);


        $quoteSettingFormService = new Preferences_Service_QuoteSetting($dealer->getDealerSettings()->getQuoteSettings());
        $form                    = $quoteSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values["save"]))
            {
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
            else
            {
                $this->redirector('index', 'index', 'admin');
            }
        }

        $this->view->form = $form;
    }
}