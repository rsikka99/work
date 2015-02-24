<?php
use MPSToolbox\Legacy\Modules\Admin\Forms\OnboardingForm;
use MPSToolbox\Legacy\Modules\Admin\Services\OnboardingOemService;
use MPSToolbox\Legacy\Modules\Admin\Services\OnboardingCompatibleService;
use Tangent\Controller\Action;

/**
 * Class Admin_OnboardingController
 */
class Admin_OnboardingController extends Action
{
    /**
     * Displays the upload form
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Onboarding');

        $dealerId       = $this->getRequest()->getUserParam('dealerId', false);
        $onboardingForm = new OnboardingForm($dealerId);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['cancel']))
            {

                if ($dealerId > 0)
                {
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
                else
                {
                    $this->redirectToRoute('admin');
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
                    // Were we successful in uploading the file?
                    if (count($oemMessages) == 0 && count($compMessages) == 0)
                    {
                        $this->_flashMessenger->addMessage(array('success' => "Files uploaded successfully!"));
                    }
                }
            }
        }

        $this->view->onboardingForm = $onboardingForm;
    }

    /**
     * Processes the OEM Pricing
     *
     * @param OnboardingForm $form
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
                $oemOnboardingService = new OnboardingOemService();
                $messages             = $oemOnboardingService->processFile($uploadedPath, $form->getValue('dealerId'));
            }
        }

        return $messages;
    }

    /**
     * Processes the OEM Pricing
     *
     * @param OnboardingForm $form
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
                $compatibleOnboardingService = new OnboardingCompatibleService();
                $messages                    = $compatibleOnboardingService->processFile($uploadedPath, $form->getValue('dealerId'));
            }
        }

        return $messages;
    }

}

