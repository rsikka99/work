<?php
/**
 *
 * @author swilder
 *
 */
class Proposalgen_Form_DeviceSwapChoice extends Twitter_Bootstrap_Form
{
    const DEVICE_TYPE_MONO      = 0;
    const DEVICE_TYPE_MONO_MFP  = 1;
    const DEVICE_TYPE_COLOR     = 2;
    const DEVICE_TYPE_COLOR_MFP = 3;


    /**
     * @var Proposalgen_Model_DeviceInstance[]
     */
    protected $_devices;

    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @var int
     */
    protected $_hardwareOptimizationId;

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
    protected $colorMfpReplacementDevices;


    /**
     * @param null $devices
     * @param      $dealerId               int
     * @param      $hardwareOptimizationId int
     * @param null $options
     */
    public function __construct ($devices, $dealerId, $hardwareOptimizationId, $options = null)
    {
        $this->_devices                = $devices;
        $this->_dealerId               = $dealerId;
        $this->_hardwareOptimizationId = $hardwareOptimizationId;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');

        // Add button(s) to form
        $submitButton = $this->createElement('button', 'Submit', array(
                                                                      'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
                                                                      'label'      => 'Re-calculate',
                                                                      'type'       => 'submit',
                                                                      'class'      => 'pull-right',
                                                                      'icon'       => 'arrow-right',
                                                                      'whiteIcon'  => true,
                                                                      'ignore'     => false,
                                                                      'title'      => 'Calculates and saves new totals based on current devices in Action column.',
                                                                 ));

        $analyzeButton           = $this->createElement('button', 'Analyze', array(
                                                                                  'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
                                                                                  'label'      => 'Auto Analyze',
                                                                                  'type'       => 'submit',
                                                                                  'class'      => 'pull-right',
                                                                                  'icon'       => 'refresh',
                                                                                  'whiteIcon'  => true,
                                                                                  'ignore'     => false,
                                                                                  'title'      => "Removes any replacement devices previously saved. Then determines the optimal devices based on target monochrome/color CPP and cost delta thershold settings.",
                                                                             ));
        $resetReplacementsButton = $this->createElement('button', 'ResetReplacements', array(
                                                                                            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_WARNING,
                                                                                            'label'      => 'Reset',
                                                                                            'type'       => 'submit',
                                                                                            'class'      => 'pull-right',
                                                                                            'icon'       => 'exclamation-sign',
                                                                                            'whiteIcon'  => true,
                                                                                            'ignore'     => false,
                                                                                            'title'      => "Sets all the replacement devices back to there default action.",

                                                                                       ));


        $this->addElements(array(
                                $resetReplacementsButton,
                                $analyzeButton,
                                $submitButton,
                           ));

        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($this->_devices as $deviceInstance)
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
                $deviceInstanceReplacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_hardwareOptimizationId);
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
            $deviceArray        = array();
            $deviceArray [0]    = 'Keep';
            $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getBlackReplacementDevices($this->_dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice                                            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
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
            $deviceArray        = array();
            $deviceArray [0]    = 'Keep';
            $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getBlackMfpReplacementDevices($this->_dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice                                            = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
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
            $deviceArray        = array();
            $deviceArray [0]    = 'Keep';
            $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorReplacementDevices($this->_dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
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
        if (!isset($this->colorMfpReplacementDevices))
        {
            $deviceArray        = array();
            $deviceArray [0]    = 'Keep';
            $replacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorMfpReplacementDevices($this->_dealerId);
            foreach ($replacementDevices as $replacementDevice)
            {
                $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== Proposalgen_Model_TonerConfig::BLACK_ONLY && $masterDevice->isCopier)
                {
                    $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }

            $this->colorMfpReplacementDevices = $deviceArray;
        }

        return $this->colorMfpReplacementDevices;
    }
}