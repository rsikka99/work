<?php

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use Tangent\Controller\Action;

/**
 * Class Preferences_ClientController
 */
class Preferences_ClientController extends Action
{
    /**
     * @var \MPSToolbox\Settings\Form\AllSettingsForm
     */
    protected $allSettingsForm;

    /**
     * @var \MPSToolbox\Settings\Service\ClientSettingsService
     */
    protected $clientSettingsService;

    public function init ()
    {
        if (!$this->getSelectedClient() instanceof \MPSToolbox\Legacy\Entities\ClientEntity)
        {
            $this->_flashMessenger->addMessage([
                "danger" => "A client is not selected."
            ]);

            $this->redirectToRoute('app.dashboard');
        }
    }

    /**
     * Handles routing the index action
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Client Settings', 'Client'];

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['save']))
            {
                $this->saveClientSettingsForm($postData, $this->getIdentity()->dealerId);
            }
            else if (isset($postData['cancel']))
            {
                $this->redirectToRoute('app.dashboard');
            }
        }
        else
        {
            $this->showClientSettingsForm($this->getIdentity()->dealerId);
        }
    }

    /**
     * Handles showing the client settings form
     *
     * @param int $dealerId
     */
    public function showClientSettingsForm ($dealerId)
    {
        $form    = $this->getAllSettingsForm();
        $service = $this->getClientSettingsService();

        // TODO lrobert: Handle better client settings logic here. This is for testing purposes only
        $clientSettings = $service->getClientSettings($this->getSelectedClient()->id, $dealerId);

        $form->currentFleetSettingsForm->populateCurrentFleetSettings($clientSettings->currentFleetSettings);
        $form->proposedFleetSettingsForm->populateProposedFleetSettings($clientSettings->proposedFleetSettings);
        $form->genericSettingsForm->populateGenericSettings($clientSettings->genericSettings);
        $form->quoteSettingsForm->populateQuoteSettings($clientSettings->quoteSettings);
        $form->optimizationSettingsForm->populateOptimizationSettings($clientSettings->optimizationSettings);

        $this->view->form = $form;
    }

    /**
     * Handles saving client settings
     *
     * @param array $data
     *
     * @param int   $dealerId
     *
     * @throws Zend_Form_Exception
     */
    public function saveClientSettingsForm ($data, $dealerId)
    {
        $form = $this->getAllSettingsForm();

        if ($form->isValid($data))
        {
            $service        = $this->getClientSettingsService();
            $clientSettings = $service->getClientSettings($this->getSelectedClient()->id, $dealerId);
            $service->saveAllSettingsForm($form, $clientSettings);
            $this->_flashMessenger->addMessage(['success' => 'Settings Saved.']);
        }
        else
        {
            $this->_flashMessenger->addMessage(['error' => 'Please correct the errors below.']);
        }


        $this->showClientSettingsForm($dealerId);
    }

    /**
     * Gets an instance of the client settings form
     *
     * @return \MPSToolbox\Settings\Form\AllSettingsForm
     */
    public function getAllSettingsForm ()
    {
        if (!isset($this->allSettingsForm))
        {
            $this->allSettingsForm = new \MPSToolbox\Settings\Form\AllSettingsForm(['tonerVendorList' => TonerVendorManufacturerMapper::getInstance()->fetchAllForDealerDropdown()]);
        }

        return $this->allSettingsForm;
    }

    /**
     * Gets an instance of the client settings service
     *
     * @return \MPSToolbox\Settings\Service\ClientSettingsService
     */
    public function getClientSettingsService ()
    {
        if (!isset($this->clientSettingsService))
        {
            $this->clientSettingsService = new \MPSToolbox\Settings\Service\ClientSettingsService();
        }

        return $this->clientSettingsService;
    }
}