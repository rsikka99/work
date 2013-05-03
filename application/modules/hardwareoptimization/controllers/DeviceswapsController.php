<?php
/**
 * Class Hardwareoptimization_DeviceswapsController
 */
class Hardwareoptimization_DeviceswapsController extends Tangent_Controller_Action
{
    /**
     * The Zend_Auth identity
     *
     * @var stdClass
     */
    protected $_identity;

    public function init ()
    {
        $this->_identity = Zend_Auth::getInstance()->getIdentity();
    }

    public function indexAction ()
    {
        $form = new Hardwareoptimization_Form_DeviceSwaps();

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            if (!isset($value['goBack']))
            {
                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();

                    // save the device swap to the database
                    $formData['dealerId'] = $this->_identity->dealerId;
                    $deviceSwap           = new Hardwareoptimization_Model_Device_Swap();
                    if ($deviceSwap->saveObject($formData))
                    {
                        $this->_flashMessenger->addMessage(array("success" => "Device swap successfully added."));
                        // If save and continue re-direct to settings page
                        if (isset($formData['saveAndContinue']))
                        {
                            $this->redirector("index", "admin", "default");
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => "Adding device swap failed. Please try again."));
                    }
                }

            }
        }

        $this->view->deviceSwap     = new Hardwareoptimization_ViewModel_DeviceSwap();
        $this->view->navigationForm = new Hardwareoptimization_Form_Hardware_Optimization_Navigation();
        $this->view->form           = $form;
    }
}