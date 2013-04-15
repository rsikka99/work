<?php
class Hardwareoptimization_IndexController extends Tangent_Controller_Action
{
    public function indexAction ()
    {
        $hardwareOptimizationId = $this->_getParam('hardwareOptimizationId');

        // Initialize the service.
        $clientId      = Proposalgen_Model_Mapper_Hardware_Optimization::getInstance()->getClientIdByHardwareOptimizationId($hardwareOptimizationId);
        $userId        = Zend_Auth::getInstance()->getIdentity()->id;
        $rmsUpload     = Proposalgen_Model_Mapper_Hardware_Optimization::getInstance()->findRmsUploadRowByHardwareOptimizationId($hardwareOptimizationId);
        $uploadService = new Proposalgen_Service_Rms_Upload($userId, $clientId, $rmsUpload);

        $form = $uploadService->getForm();

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
                    $hardwareOptimization              = Proposalgen_Model_Mapper_Hardware_Optimization::getInstance()->find($hardwareOptimizationId);
                    $hardwareOptimization->rmsUploadId = $rmsUpload->id;
                    Proposalgen_Model_Mapper_Hardware_Optimization::getInstance()->save($hardwareOptimization);

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
//                     Call the base controller to send us to the next logical step in the proposal.
//                    $this->gotoNextStep();
                }
            }
        }

        $this->view->form           = $form;
        $this->view->rmsUpload      = $uploadService->rmsUpload;
        $navigationButtons          = ($uploadService->rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);
    }

}

