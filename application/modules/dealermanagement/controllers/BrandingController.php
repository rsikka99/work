<?php

/**
 * Class Dealermanagement_BrandingController
 */
class Dealermanagement_BrandingController extends Tangent_Controller_Action
{
    /**
     * @var stdClass
     */
    protected $_mpsSession;

    /**
     * @var stdClass
     */
    protected $_identity;

    public function init ()
    {
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
    }

    /**
     * This is where we edit dealer branding
     */
    public function indexAction ()
    {
        $this->view->headTitle('Branding');
        $this->view->headTitle('Dealer Management');

        $dealerBrandingService = new Dealermanagement_Service_Dealer_Branding();
        $form                  = $dealerBrandingService->getDealerBrandingForm();

        $dealerId = $this->_identity->dealerId;
        $create   = false;

        $dealer = Application_Model_Mapper_Dealer::getInstance()->find($dealerId);

        $dealerBranding = Application_Model_Mapper_Dealer_Branding::getInstance()->find($dealerId);
        if (!$dealerBranding instanceof Application_Model_Dealer_Branding)
        {
            $create                          = true;
            $dealerBranding                  = new Application_Model_Dealer_Branding();
            $dealerBranding->dealerId        = $dealerId;
            $dealerBranding->dealerName      = $dealer->dealerName;
            $dealerBranding->shortDealerName = $dealer->dealerName;
        }

        $form->populate($dealerBranding->toArray());

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['cancel']))
            {
                $this->redirector('index', 'index', 'admin');
            }
            else
            {
                if ($create)
                {
                    $dealerBrandingService->create($postData, $dealerId);
                }
                else
                {
                    $dealerBrandingService->update($postData, $dealerId);
                }

                if ($dealerBrandingService->hasErrors())
                {
                    foreach ($dealerBrandingService->getErrors() as $errorType => $errorMessage)
                    {
                        $this->_flashMessenger->addMessage(array('error' => $errorMessage));
                    }
                }
                else
                {
                    My_Brand::resetDealerBrandingCache();
                    Application_Service_Less::compileReportStyles(true);
                    $this->_flashMessenger->addMessage(array('success' => 'Branding Saved.'));
                }
            }
        }

        $this->view->form = $form;
    }
}

