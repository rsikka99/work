<?php
/**
 *
 * @author swilder
 *
 */
class Proposalgen_Form_DeviceSwapChoice extends Zend_Form
{
    /**
     *
     * @var Proposalgen_Model_Report
     */
    protected $report;
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

    public function __construct($report, $options = NULL)
    {
        $this->report = $report;
        parent::__construct($options);
    }

    public function init()
    {
        $this->setMethod('post');

        // Add button(s) to form
        $submitButton = $this->createElement('submit', 'Submit', array(
            'label' => "Re-calculate",
            'ignore' => false,
            'title' => 'Calculates and saves new totals based on current devices in Action column.'
        ));
        $cancelButton = $this->createElement('submit', 'Cancel', array(
            'label' => "Save And Continue",
            'ignore' => false,
            "title" => "Calculates and saves new totals based on current device in Action column. Proceeds to next step."
        ));
        $analyzeButton = $this->createElement('submit', 'Analyze', array(
            'label' => "Auto Analyze",
            'ignore' => false,
            'title' => "Removes any replacement devices previously saved. Then determines the optimal devices based on target monochrome/color CPP and cost delta thershold settings."
        ));
        $resetReplacementsButton = $this->createElement('submit', 'ResetReplacements', array(
            'label' => "Reset",
            'ignore' => false,
            'title' => "Sets all the replacement devices back to there default action.",
        ));

        $this->addElements(array(
            $submitButton,
            $cancelButton,
            $analyzeButton,
            $resetReplacementsButton
        ));

        /**
         * Data for select boxes
         */
        $mappedDevices = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->getDevicesForReport($this->report->id);

        echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
        var_dump($mappedDevices);
        die();

        // Get all proposal devices
        // $proposalDevices = $this->proposal->getDevices()->getPurchased();

        /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
        foreach ($mappedDevices as $deviceInstance) {
            // Get replacement devices for each type of device
            if ($deviceInstance->getAction() !== Application_Model_DeviceInstance::ACTION_RETIRE) {
                if ($deviceInstance->getMasterDevice()->getTonerConfigId() === Application_Model_TonerConfig::BLACK_ONLY) {
                    if ($deviceInstance->getMasterDevice()->getIsCopier()) {
                        $replacementDevices = $this->getBlackMfpReplacementDevices();
                    } else {
                        $replacementDevices = $this->getBlackReplacementDevices();
                    }
                } else {
                    if ($deviceInstance->getMasterDevice()->getIsCopier()) {
                        $replacementDevices = $this->getColorMfpReplacementDevicecs();
                    } else {
                        $replacementDevices = $this->getColorReplacementDevices();
                    }
                }

                if ($deviceInstance instanceof Application_Model_UnknownDeviceInstance) {
                    $deviceType = 'unknownDeviceInstance_';
                } else if ($deviceInstance instanceof Application_Model_DeviceInstance) {
                    $deviceType = 'deviceInstance_';
                }

                $deviceInstanceReplacementMasterDevice = $deviceInstance->getReplacementMasterDevice();
            } else {
                $replacementDevices = array();
            }

            $replacementDevices[0] = $deviceInstance->getAction();

            // Create an element for each device
            // Device list per manufacturer
            $deviceElement = $this->createElement('select', $deviceType . $deviceInstance->getDeviceInstanceId(), array(
                'label' => 'Device: ',
                'attribs' => array(
                    'style' => 'width: 100%'
                ),
                'value' => ($deviceInstanceReplacementMasterDevice) ? $deviceInstanceReplacementMasterDevice->getMasterDeviceId() : 0
            ));

            $this->addElement($deviceElement);

            /*
             * If the master device device does not exist in our array we need to add it as it is replaced anyways....
             * o.O
             */
            if ($deviceInstanceReplacementMasterDevice && !array_key_exists($deviceInstanceReplacementMasterDevice->getMasterDeviceId(), $replacementDevices)) {
                $replacementDevices [$deviceInstanceReplacementMasterDevice->getMasterDeviceId()] = $deviceInstanceReplacementMasterDevice->getManufacturer()->getManufacturerName() . " " . $deviceInstanceReplacementMasterDevice->getPrinterModel();
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
                    'tag' => 'div',
                    'class' => 'form-actions',
                    'openOnly' => true,
                    'placement' => Zend_Form_Decorator_Abstract::PREPEND
                )
            )
        ));
        $cancelButton->setDecorators(array(
            'ViewHelper',
            array(
                'HtmlTag',
                array(
                    'tag' => 'div',
                    'closeOnly' => true,
                    'placement' => Zend_Form_Decorator_Abstract::APPEND
                )
            )
        ));
    }

    /**
     * Getter for $proposal
     *
     * @return Application_Model_Proposal_Abstract
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Getter for $blackReplacementDevices
     *
     * @return field_type
     */
    public function getBlackReplacementDevices()
    {
        if (!isset($this->blackReplacementDevices)) {
            $deviceArray = array();
            $deviceArray [0] = 'Keep';
            /* @var $replacementDevices Default_Model_Replacement_Device */
            $replacementDevices = Default_Model_Mapper_Replacement_Device::getInstance()->getBlackReplacementDevices();
            foreach ($replacementDevices as $replacementDevice) {
                /* @var $masterDevice Application_Model_MasterDevice */
                $masterDevice = Application_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDeviceId());
                $deviceArray [$replacementDevice->getMasterDeviceId()] = $masterDevice->getManufacturer()->getManufacturerName() . ' ' . $masterDevice->getPrinterModel();
            }

            $this->blackReplacementDevices = $deviceArray;
        }
        return $this->blackReplacementDevices;
    }

    /**
     * Getter for $blackMfpReplacementDevices
     *
     * @return field_type
     */
    public function getBlackMfpReplacementDevices()
    {
        if (!isset($this->blackMfpReplacementDevices)) {
            $deviceArray = array();
            $deviceArray [0] = 'Keep';
            /* @var $replacementDevices Default_Model_Replacement_Device */
            $replacementDevices = Default_Model_Mapper_Replacement_Device::getInstance()->getBlackMfpReplacementDevices();
            foreach ($replacementDevices as $replacementDevice) {
                /* @var $masterDevice Application_Model_MasterDevice */
                $masterDevice = Application_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDeviceId());
                $deviceArray [$replacementDevice->getMasterDeviceId()] = $masterDevice->getManufacturer()->getManufacturerName() . ' ' . $masterDevice->getPrinterModel();
            }

            $this->blackMfpReplacementDevices = $deviceArray;
        }
        return $this->blackMfpReplacementDevices;
    }

    /**
     * Getter for $colorReplacementDevices
     *
     * @return field_type
     */
    public function getColorReplacementDevices()
    {
        if (!isset($this->colorReplacementDevices)) {
            $deviceArray = array();
            $deviceArray [0] = 'Keep';
            /* @var $replacementDevices Default_Model_Replacement_Device */
            $replacementDevices = Default_Model_Mapper_Replacement_Device::getInstance()->getColorReplacementDevices();
            foreach ($replacementDevices as $replacementDevice) {
                /* @var $masterDevice Application_Model_MasterDevice */
                $masterDevice = Application_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDeviceId());
                if ($masterDevice->getTonerConfigId() !== Application_Model_TonerConfig::BLACK_ONLY) {
                    $deviceArray [$replacementDevice->getMasterDeviceId()] = $masterDevice->getManufacturer()->getManufacturerName() . ' ' . $masterDevice->getPrinterModel();
                }
            }

            $this->colorReplacementDevices = $deviceArray;
        }
        return $this->colorReplacementDevices;
    }

    /**
     * Getter for $colorMfpReplacementDevicecs
     *
     * @return field_type
     */
    public function getColorMfpReplacementDevicecs()
    {
        if (!isset($this->colorMfpReplacementDevicecs)) {
            $deviceArray = array();
            $deviceArray [0] = 'Keep';
            /* @var $replacementDevices Default_Model_Replacement_Device */
            $replacementDevices = Default_Model_Mapper_Replacement_Device::getInstance()->getColorMfpReplacementDevices();
            foreach ($replacementDevices as $replacementDevice) {
                /* @var $masterDevice Application_Model_MasterDevice */
                $masterDevice = Application_Model_Mapper_MasterDevice::getInstance()->find($replacementDevice->getMasterDeviceId());
                if ($masterDevice->getTonerConfigId() !== Application_Model_TonerConfig::BLACK_ONLY && $masterDevice->getIsCopier()) {
                    $deviceArray [$replacementDevice->getMasterDeviceId()] = $masterDevice->getManufacturer()->getManufacturerName() . ' ' . $masterDevice->getPrinterModel();
                }
            }

            $this->colorMfpReplacementDevicecs = $deviceArray;
        }
        return $this->colorMfpReplacementDevicecs;
    }
}