<?php
/**
 *
 * @author swilder
 *
 */
class Proposalgen_Form_DeviceSwapChoice extends Zend_Form
{
    const DEVICETYPE_MONO = 0;
    const DEVICETYPE_MONO_MFP = 1;
    const DEVICETYPE_COLOR = 2;
    const DEVICETYPE_COLOR_MFP = 3;

    /**
     *
     * @var Proposalgen_Model_Proposal_OfficeDepot
     */
    protected $proposal;
    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $blackReplacementDevices;
    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $blackMfpReplacementDevices;
    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $colorReplacementDevices;
    /**
     *
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $colorMfpReplacementDevicecs;

    public function __construct ($proposal, $options = null)
    {
        $this->proposal = $proposal;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');

        // Add button(s) to form
        $submitButton            = $this->createElement('submit', 'Submit', array(
                                                                                 'label'  => "Re-calculate",
                                                                                 'ignore' => false,
                                                                                 'title'  => 'Calculates and saves new totals based on current devices in Action column.'
                                                                            ));
        $cancelButton            = $this->createElement('submit', 'Cancel', array(
                                                                                 'label'  => "Save And Continue",
                                                                                 'ignore' => false,
                                                                                 "title"  => "Calculates and saves new totals based on current device in Action column. Proceeds to next step."
                                                                            ));
        $analyzeButton           = $this->createElement('submit', 'Analyze', array(
                                                                                  'label'  => "Auto Analyze",
                                                                                  'ignore' => false,
                                                                                  'title'  => "Removes any replacement devices previously saved. Then determines the optimal devices based on target monochrome/color CPP and cost delta thershold settings."
                                                                             ));
        $resetReplacementsButton = $this->createElement('submit', 'ResetReplacements', array(
                                                                                            'label'  => "Reset",
                                                                                            'ignore' => false,
                                                                                            'title'  => "Sets all the replacement devices back to there default action.",
                                                                                       ));

        $this->addElements(array(
                                $submitButton,
                                $cancelButton,
                                $analyzeButton,
                                $resetReplacementsButton
                           ));

        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($this->proposal->getPurchasedDevices() as $deviceInstance)
        {
            // Get replacement devices for each type of device
            if ($deviceInstance->getAction() !== 'Retire')
            {
                if ($deviceInstance->getMasterDevice()->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $replacementDevices = $this->getBlackMfpReplacementDevices();
                    }
                    else
                    {
                        $replacementDevices = $this->getBlackReplacementDevices();
                    }
                }
                else
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $replacementDevices = $this->getColorMfpReplacementDevices();
                    }
                    else
                    {
                        $replacementDevices = $this->getColorReplacementDevices();
                    }
                }
                $deviceInstanceReplacementMasterDevice = $deviceInstance->getReplacementMasterDevice();
            }
            else
            {
                $replacementDevices                    = array();
                $deviceInstanceReplacementMasterDevice = null;
            }

            $deviceType = 'deviceInstance_';

            $replacementDevices[0] = $deviceInstance->getAction();
            // Create an element for each device Device list per manufacturer
            $deviceElement = $this->createElement('select', $deviceType . $deviceInstance->id, array(
                                                                                                    'label'   => 'Device: ',
                                                                                                    'attribs' => array(
                                                                                                        'style' => 'width: 100%'
                                                                                                    ),
                                                                                                    'value'   => ($deviceInstanceReplacementMasterDevice) ? $deviceInstanceReplacementMasterDevice->id : 0
                                                                                               ));

            $this->addElement($deviceElement);

            /*
             * If the master device device does not exist in our array we need to add it as it is replaced anyways....
             * o.O
             */
            if ($deviceInstanceReplacementMasterDevice && !array_key_exists($deviceInstanceReplacementMasterDevice->id, $replacementDevices))
            {
                $replacementDevices [$deviceInstanceReplacementMasterDevice->id] = $deviceInstanceReplacementMasterDevice->getManufacturer()->fullname . " " . $deviceInstanceReplacementMasterDevice->modelName;
            }
            $deviceElement->setMultiOptions($replacementDevices);
        }

        /**
         * Form element decorators
         */
        $submitButton->setDecorators(array(
                                          'ViewHelper',
                                          array(
                                              'HtmlTag',
                                              array(
                                                  'tag'       => 'div',
                                                  'class'     => 'form-actions',
                                                  'openOnly'  => true,
                                                  'placement' => Zend_Form_Decorator_Abstract::PREPEND
                                              )
                                          )
                                     ));
        $cancelButton->setDecorators(array(
                                          'ViewHelper',
                                          array(
                                              'HtmlTag',
                                              array(
                                                  'tag'       => 'div',
                                                  'closeOnly' => true,
                                                  'placement' => Zend_Form_Decorator_Abstract::APPEND
                                              )
                                          )
                                     ));
    }

    /**
     * Getter for $proposal
     *
     * @return Proposalgen_Model_Proposal_OfficeDepot
     */
    public function getProposal ()
    {
        return $this->proposal;
    }

    /**
     * Getter for $blackReplacementDevices
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getBlackReplacementDevices ()
    {
        if (!isset($this->blackReplacementDevices))
        {
            $deviceArray     = array();
            $deviceArray [0] = 'Keep';
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getBlackReplacementDevices();
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice                                          = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->id);
                $deviceArray [$replacementDevice->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }

            $this->blackReplacementDevices = $deviceArray;
        }

        return $this->blackReplacementDevices;
    }

    /**
     * Getter for $blackMfpReplacementDevices
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getBlackMfpReplacementDevices ()
    {
        if (!isset($this->blackMfpReplacementDevices))
        {
            $deviceArray     = array();
            $deviceArray [0] = 'Keep';
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getBlackMfpReplacementDevices();
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice                                          = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->id);
                $deviceArray [$replacementDevice->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }

            $this->blackMfpReplacementDevices = $deviceArray;
        }

        return $this->blackMfpReplacementDevices;
    }

    /**
     * Getter for $colorReplacementDevices
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getColorReplacementDevices ()
    {
        if (!isset($this->colorReplacementDevices))
        {
            $deviceArray     = array();
            $deviceArray [0] = 'Keep';
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getColorReplacementDevices();
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    $deviceArray [$replacementDevice->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }
            $this->colorReplacementDevices = $deviceArray;
        }
        return $this->colorReplacementDevices;
    }

    /**
     * Getter for $colorMfpReplacementDevicecs
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getColorMfpReplacementDevices ()
    {
        if (!isset($this->colorMfpReplacementDevicecs))
        {
            $deviceArray     = array();
            $deviceArray [0] = 'Keep';
            $replacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getColorMfpReplacementDevices();
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY && $masterDevice->isCopier)
                {
                    $deviceArray [$replacementDevice->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }

            $this->colorMfpReplacementDevicecs = $deviceArray;
        }

        return $this->colorMfpReplacementDevicecs;
    }
}