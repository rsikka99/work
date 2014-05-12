<?php

/**
 * Class Admin_OnboardingController
 */
class Admin_OnboardingController extends Tangent_Controller_Action
{
    /**
     * Displays the upload form
     */
    public function indexAction ()
    {
        $this->view->headTitle('Onboarding');

        $dealerId       = $this->getRequest()->getUserParam('dealerId', false);
        $onboardingForm = new Admin_Form_Onboarding($dealerId);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['cancel']))
            {

                if ($dealerId > 0)
                {
                    $this->redirector('view', 'dealer', null, array('id' => $dealerId));
                }
                else
                {
                    $this->redirector('index');
                }
            }
            else
            {
                if ($onboardingForm->isValid($postData))
                {
                    $oemMessages = $this->_processOemPricing($onboardingForm);

                    foreach ($oemMessages as $message)
                    {
                        $this->_flashMessenger->addMessage(array('warning' => 'OEM File: ' . $message));
                    }

                    $compMessages = $this->_processCompPricing($onboardingForm);

                    foreach ($compMessages as $message)
                    {
                        $this->_flashMessenger->addMessage(array('warning' => 'COMP File: ' . $message));
                    }

                }
            }
        }

        $this->view->onboardingForm = $onboardingForm;
    }

    /**
     * Processes the OEM Pricing
     *
     * @param Admin_Form_Onboarding $form
     *
     * @return array
     */
    protected function _processOemPricing ($form)
    {
        $messages   = array();
        $oemPricing = $form->getOemPricingElement();
        if ($oemPricing->isUploaded())
        {
            $oemPricing->setDestination(DATA_PATH . '/uploads/');
            if ($oemPricing->receive())
            {
                $uploadedPath         = $oemPricing->getFileName();
                $oemOnboardingService = new Admin_Service_Onboarding_Oem();
                $messages             = $oemOnboardingService->processFile($uploadedPath, $form->getValue('dealerId'));
            }
        }

        return $messages;
    }

    /**
     * Processes the OEM Pricing
     *
     * @param Admin_Form_Onboarding $form
     *
     * @return array|bool|string
     */
    protected function _processCompPricing ($form)
    {
        $messages    = array();
        $compPricing = $form->getCompPricingElement();
        if ($compPricing->isUploaded())
        {
            $compPricing->setDestination(DATA_PATH . '/uploads/');
            if ($compPricing->receive())
            {
                $uploadedPath                = $compPricing->getFileName();
                $compatibleOnboardingService = new Admin_Service_Onboarding_Compatible();
                $messages                    = $compatibleOnboardingService->processFile($uploadedPath, $form->getValue('dealerId'));
            }
        }

        return $messages;
    }

}

