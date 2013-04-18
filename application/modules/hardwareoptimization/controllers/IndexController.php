<?php
class Hardwareoptimization_IndexController extends Tangent_Controller_Action
{

    /**
     * @var int
     */
    protected $_selectedClientId;

    /**
     * The namespace for our mps application
     *
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * The identity of the currently logged in user
     *
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization
     */
    protected $_hardwareOptimization;

    /**
     * Initialize the controller
     */
    public function init ()
    {
        $this->_mpsSession           = new Zend_Session_Namespace('mps-tools');
        $this->_identity             = Zend_Auth::getInstance()->getIdentity();
        $this->_hardwareOptimization = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->find($this->_mpsSession->hardwareOptimizationId);

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            // Make sure the selected client is ours!
            if ($client && $client->dealerId == Zend_Auth::getInstance()->getIdentity()->dealerId)
            {
                $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
                $this->view->selectedClientId = $this->_selectedClientId;
            }
        }
    }

    public function indexAction ()
    {
        // Initialize the service.
        $uploadService = new Proposalgen_Service_Rms_Upload($this->_identity->id, $this->_selectedClientId, $this->_hardwareOptimization->rmsUploadId);
        $form          = $uploadService->getForm();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else if (isset($values ["performUpload"]))
            {
                $success = $uploadService->processUpload($values);
                if ($success)
                {
                    $rmsUpload = $uploadService->rmsUpload;

                    // Save the hardware optimization object with the new id.
                    $this->_hardwareOptimization->rmsUploadId = $rmsUpload->id;
                    Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->save($this->_hardwareOptimization);

                    $this->_flashMessenger->addMessage(array("success" => "Upload was successful."));
                }
                else
                {
                    $this->_flashMessenger->addMessage(array("danger" => $uploadService->errorMessages));
                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $count = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->countRowsForRmsUpload($uploadService->rmsUpload->id);
                if ($count < 2)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => "You must have at least 2 valid devices to continue."
                                                       ));
                }
                else
                {
                    $this->redirector('settings');
                }
            }
        }

        $this->view->form           = $form;
        $this->view->rmsUpload      = $uploadService->rmsUpload;
        $navigationButtons          = ($uploadService->rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);
    }

    public function settingsAction ()
    {
        $user   = Application_Model_Mapper_User::getInstance()->find($this->_identity->id);

        $hardwareOptimizationService = new Preferences_Service_HardwareoptimizationSetting($this->_hardwareOptimization->getHardwareOptimizationSetting()->toArray());

        $defaultHardwareOptimizationSettings = $user->getDealer()->getDealerSettings()->getHardwareOptimizationSettings();
        $defaultHardwareOptimizationSettings->populate($user->getUserSettings()->getHardwareOptimizationSettings()->toArray());
        $this->view->form = $hardwareOptimizationService->getFormWithDefaults($defaultHardwareOptimizationSettings->toArray());
    }
}