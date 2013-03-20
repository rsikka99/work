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
        $userQuoteSettings = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting(Zend_Auth::getInstance()->getIdentity()->id);
        $quoteService = new Preferences_Service_QuoteSetting($userQuoteSettings->toArray());
        $form         = $quoteService->getFormWithDefaults();
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $quoteService->update($values);

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

    public function dealerAction ()
    {
        $dealerQuoteSetting = Quotegen_Model_Mapper_DealerQuoteSetting::getInstance()->fetchDealerQuoteSetting(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $quoteService = new Preferences_Service_QuoteSetting($dealerQuoteSetting->toArray());
        $form         = $quoteService->getForm();

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $quoteService->update($values);

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