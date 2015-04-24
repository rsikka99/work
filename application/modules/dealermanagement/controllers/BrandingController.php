<?php
use MPSToolbox\Legacy\Mappers\DealerBrandingMapper;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerBrandingModel;
use MPSToolbox\Legacy\Modules\DealerManagement\Services\DealerBrandingService;
use MPSToolbox\Legacy\Services\LessCssService;
use Tangent\Controller\Action;

/**
 * Class Dealermanagement_BrandingController
 */
class Dealermanagement_BrandingController extends Action
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
        $this->_pageTitle = ['Company Branding', 'Company'];

        $dealerBrandingService = new DealerBrandingService();
        $form                  = $dealerBrandingService->getDealerBrandingForm();

        $dealerId = $this->_identity->dealerId;
        $create   = false;

        $dealer = DealerMapper::getInstance()->find($dealerId);

        $dealerBranding = DealerBrandingMapper::getInstance()->find($dealerId);
        if (!$dealerBranding instanceof DealerBrandingModel)
        {
            $create                          = true;
            $dealerBranding                  = new DealerBrandingModel();
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
                $this->redirectToRoute('company');
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
                        $this->_flashMessenger->addMessage(['error' => $errorMessage]);
                    }
                }
                else
                {
                    My_Brand::resetDealerBrandingCache();
                    LessCssService::compileReportStyles(true);
                    $this->_flashMessenger->addMessage(['success' => 'Branding Saved.']);
                }
            }
        }

        $this->view->form = $form;
    }
}

