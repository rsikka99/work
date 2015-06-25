<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DealerMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DealerModel;
use Tangent\Controller\Action;

/**
 * Class Preferences_DealerController
 */
class Preferences_DealerController extends Action
{
    /**
     * @var \MPSToolbox\Settings\Form\AllSettingsForm
     */
    protected $allSettingsForm;

    /**
     * @var \MPSToolbox\Settings\Service\DealerSettingsService
     */
    protected $dealerSettingsService;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_identity;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * Handles routing the index action
     */
    public function indexAction()
    {
        $this->_pageTitle = ['Company Settings', 'Company'];

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['save'])) {
                $this->saveDealerSettingsForm($postData);
            } else if (isset($postData['cancel'])) {
                $this->redirectToRoute('company');
            }
        } else {
            $this->showDealerSettingsForm();
        }
    }

    public function shopAction()
    {
        $this->_pageTitle = ['E-Commerce Settings', 'Company'];

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['submit'])) {
                $this->saveShopSettingsForm($postData);
                return;
            }
            $this->redirectToRoute('company');
            return;
        }
        $this->showShopSettingsForm();
    }

    /**
     * Handles showing the dealer settings form
     */
    public function showDealerSettingsForm()
    {
        $form = $this->getAllSettingsForm();
        $service = $this->getDealerSettingsService();

        // TODO lrobert: Handle better dealer settings logic here. This is for testing purposes only
        $dealerSettings = $service->getDealerSettings($this->getIdentity()->dealerId);

        $form->currentFleetSettingsForm->populateCurrentFleetSettings($dealerSettings->currentFleetSettings);
        $form->proposedFleetSettingsForm->populateProposedFleetSettings($dealerSettings->proposedFleetSettings);
        $form->genericSettingsForm->populateGenericSettings($dealerSettings->genericSettings);
        $form->quoteSettingsForm->populateQuoteSettings($dealerSettings->quoteSettings);
        $form->optimizationSettingsForm->populateOptimizationSettings($dealerSettings->optimizationSettings);

        $this->view->form = $form;
    }

    public function showShopSettingsForm()
    {
        $form = $this->getShopSettingsForm();
        $service = $this->getDealerSettingsService();
        $dealerSettings = $service->getDealerSettings($this->getIdentity()->dealerId);
        $form->populate($dealerSettings);
        $this->view->form = $form;
    }

    /**
     * Handles saving dealer settings
     *
     * @param array $data
     *
     * @throws Zend_Form_Exception
     */
    public function saveDealerSettingsForm($data)
    {
        $form = $this->getAllSettingsForm();

        if ($form->isValid($data)) {
            $service = $this->getDealerSettingsService();
            $dealerSettings = $service->getDealerSettings($this->getIdentity()->dealerId);
            $service->saveAllSettingsForm($form, $dealerSettings);
            $this->_flashMessenger->addMessage(['success' => 'Settings Saved.']);
        } else {
            $this->_flashMessenger->addMessage(['error' => 'Please correct the errors below.']);
        }


        $this->showDealerSettingsForm();
    }

    public function saveShopSettingsForm($data)
    {
        $form = $this->getShopSettingsForm();
        if ($form->isValid($data)) {
            $service = $this->getDealerSettingsService();
            $dealerSettings = $service->getDealerSettings($this->getIdentity()->dealerId);
            $service->saveShopSettingsForm($form, $dealerSettings);
            $this->_flashMessenger->addMessage(['success' => 'Settings Saved.']);
            $this->redirectToRoute('company');
            return;
        }
        $this->_flashMessenger->addMessage(['error' => 'Please correct the errors below.']);
        $this->showShopSettingsForm();
    }

    /**
     * Gets an instance of the dealer settings form
     *
     * @return \MPSToolbox\Settings\Form\AllSettingsForm
     */
    public function getAllSettingsForm()
    {
        if (!isset($this->allSettingsForm)) {
            $this->allSettingsForm = new \MPSToolbox\Settings\Form\AllSettingsForm(['tonerVendorList' => TonerVendorManufacturerMapper::getInstance()->fetchAllForDealerDropdown()]);
        }

        return $this->allSettingsForm;
    }

    public function getShopSettingsForm()
    {
        if (!isset($this->shopSettingsForm)) {
            $this->shopSettingsForm = new \MPSToolbox\Settings\Form\ShopSettingsForm();
        }

        return $this->shopSettingsForm;
    }

    /**
     * Gets an instance of the dealer settings service
     *
     * @return \MPSToolbox\Settings\Service\DealerSettingsService
     */
    public function getDealerSettingsService()
    {
        if (!isset($this->dealerSettingsService)) {
            $this->dealerSettingsService = new \MPSToolbox\Settings\Service\DealerSettingsService();
        }

        return $this->dealerSettingsService;
    }

}
