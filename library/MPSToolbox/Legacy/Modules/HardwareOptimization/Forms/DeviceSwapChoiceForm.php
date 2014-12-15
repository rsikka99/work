<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonDefaultMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonCategoryModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use Zend_Form;

/**
 * Class DeviceSwapChoiceForm
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Forms
 */
class DeviceSwapChoiceForm extends Zend_Form
{
    const DEVICE_TYPE_MONO      = 0;
    const DEVICE_TYPE_MONO_MFP  = 1;
    const DEVICE_TYPE_COLOR     = 2;
    const DEVICE_TYPE_COLOR_MFP = 3;

    /**
     * @var DeviceInstanceModel[]
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
     * @var DeviceSwapModel[]
     */
    protected $blackReplacementDevices;

    /**
     *
     * @var DeviceSwapModel[]
     */
    protected $blackMfpReplacementDevices;

    /**
     *
     * @var DeviceSwapModel[]
     */
    protected $colorReplacementDevices;

    /**
     *
     * @var DeviceSwapModel[]
     */
    protected $colorMfpReplacementDevices;

    /**
     * @param DeviceInstanceModel[] $devices
     * @param int                   $dealerId
     * @param int                   $hardwareOptimizationId
     * @param null|array            $options
     */
    public function __construct ($devices, $dealerId, $hardwareOptimizationId, $options = null)
    {
        $this->_devices                = $devices;
        $this->_dealerId               = $dealerId;
        $this->_hardwareOptimizationId = $hardwareOptimizationId;
        parent::__construct($options);
    }

    /**
     * Initializes the form
     */
    public function init ()
    {
        foreach ($this->_devices as $deviceInstance)
        {
            /**
             *
             * Get the proper replacement device list
             *
             */
            if ($deviceInstance->getMasterDevice()->isColor())
            {
                if ($deviceInstance->getMasterDevice()->isMfp())
                {
                    $replacementDevices = $this->getColorMfpReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
                else
                {
                    $replacementDevices = $this->getColorReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
            }
            else
            {
                if ($deviceInstance->getMasterDevice()->isMfp())
                {
                    $replacementDevices = $this->getBlackMfpReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
                else
                {
                    $replacementDevices = $this->getBlackReplacementDevices($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
                }
            }

            /**
             *
             * Setup the dropdown for actions and replacements
             *
             */
            $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimizationId);

            $replacementDeviceOptions = array(
                "Actions:" => array(
                    HardwareOptimizationDeviceInstanceModel::ACTION_KEEP   => HardwareOptimizationDeviceInstanceModel::ACTION_KEEP,
                    HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE => HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE . "/Migrate (Low Page Volume)",
                    HardwareOptimizationDeviceInstanceModel::ACTION_DNR    => HardwareOptimizationDeviceInstanceModel::ACTION_DNR . "(Replace when broken)",
                )
            );

            /**
             * Only display the replacement option if we have devices available.
             */
            if (count($replacementDevices) > 0)
            {
                $replacementDeviceOptions["Optimize With:"] = $replacementDevices;
            }

            // Eligible replacement master devices changed from time to time. Old selections should still show up for the sake of usability.
            if ($hardwareOptimizationDeviceInstance->masterDeviceId > 0 && !array_key_exists($hardwareOptimizationDeviceInstance->masterDeviceId, $replacementDevices['Outdated Replacement:']))
            {
                $replacementMasterDevice = $hardwareOptimizationDeviceInstance->getMasterDevice();
                if ($replacementMasterDevice instanceof MasterDeviceModel)
                {
                    $replacementDevices [$replacementMasterDevice->id] = $replacementMasterDevice->getManufacturer()->fullname . " " . $replacementMasterDevice->modelName;
                }
            }

            $this->addElement('select', 'deviceInstance_' . $deviceInstance->id, array(
                'label'        => 'Device: ',
                'attribs'      => array(
                    'style'                   => 'width: 100%',
                    'data-device-instance-id' => $deviceInstance->id,
                ),
                'value'        => ($hardwareOptimizationDeviceInstance->masterDeviceId > 0) ? $hardwareOptimizationDeviceInstance->masterDeviceId : $hardwareOptimizationDeviceInstance->action,
                'multiOptions' => $replacementDeviceOptions,
            ));


            /**
             *
             * Setup the dropdown the replacement reason
             *
             */
            $deviceSwapReason = $hardwareOptimizationDeviceInstance->getDeviceSwapReason();

            $reasons = false;

            if ($hardwareOptimizationDeviceInstance->action == HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE && $hardwareOptimizationDeviceInstance->masterDeviceId > 0)
            {
                $reasons = $this->getDeviceSwapsByCategory(DeviceSwapReasonCategoryModel::HAS_REPLACEMENT);
            }
            else if ($hardwareOptimizationDeviceInstance->action == HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE && $hardwareOptimizationDeviceInstance->masterDeviceId > 0)
            {
                $reasons = $this->getDeviceSwapsByCategory(DeviceSwapReasonCategoryModel::HAS_FUNCTIONALITY_REPLACEMENT);
            }
            else if ($hardwareOptimizationDeviceInstance->action == HardwareOptimizationDeviceInstanceModel::ACTION_DNR)
            {
                $reasons = $this->getDeviceSwapsByCategory(DeviceSwapReasonCategoryModel::FLAGGED);
            }

            if ($deviceSwapReason->id > 0 && !in_array($deviceSwapReason->id, $reasons))
            {
                $reasons[$deviceSwapReason->id] = $deviceSwapReason->reason;
            }

            if ($reasons !== false)
            {
                $this->addElement('select', 'deviceInstanceReason_' . $deviceInstance->id, array(
                    'label'        => ': ',
                    'attribs'      => array(
                        'style'                   => 'width: 100%',
                        'data-device-instance-id' => $deviceInstance->id,
                    ),
                    'multiOptions' => $reasons,
                    'value'        => ($deviceSwapReason->id) ? $deviceSwapReason->id : 0,
                ));
            }


        }
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardwareoptimization/device-swap-choice-form.phtml',
                    'devices'    => $this->_devices
                )
            )
        ));
    }

    /**
     * Getter for $blackReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return DeviceSwapModel []
     */
    public function getBlackReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = DeviceSwapMapper::getInstance()->getBlackReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < (int)$replacementDevice->maximumPageCount && $monthlyPageCounts > (int)$replacementDevice->minimumPageCount)
            {
                $masterDevice                                            = MasterDeviceMapper::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }
        }

        $this->blackReplacementDevices = $deviceArray;

        return $this->blackReplacementDevices;
    }

    /**
     * Getter for $blackMfpReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return DeviceSwapModel []
     */
    public function getBlackMfpReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = DeviceSwapMapper::getInstance()->getBlackMfpReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice                                            = MasterDeviceMapper::getInstance()->find($replacementDevice->getMasterDevice()->id);
                $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
            }
        }

        $this->blackMfpReplacementDevices = $deviceArray;


        return $this->blackMfpReplacementDevices;
    }

    /**
     * Getter for $colorReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return DeviceSwapModel []
     */
    public function getColorReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = DeviceSwapMapper::getInstance()->getColorReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice = MasterDeviceMapper::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== TonerConfigModel::BLACK_ONLY)
                {
                    $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }
        }
        $this->colorReplacementDevices = $deviceArray;


        return $this->colorReplacementDevices;
    }

    /**
     * Getter for $colorMfpReplacementDevices
     *
     * @param $monthlyPageCounts
     *
     * @return DeviceSwapModel []
     */
    public function getColorMfpReplacementDevices ($monthlyPageCounts)
    {
        $deviceArray        = array();
        $replacementDevices = DeviceSwapMapper::getInstance()->getColorMfpReplacementDevices($this->_dealerId);
        foreach ($replacementDevices as $replacementDevice)
        {
            if ($monthlyPageCounts < $replacementDevice->maximumPageCount && $monthlyPageCounts > $replacementDevice->minimumPageCount)
            {
                $masterDevice = MasterDeviceMapper::getInstance()->find($replacementDevice->getMasterDevice()->id);
                if ($masterDevice->tonerConfigId !== TonerConfigModel::BLACK_ONLY && $masterDevice->isMfp())
                {
                    $deviceArray [$replacementDevice->getMasterDevice()->id] = $masterDevice->getManufacturer()->fullname . ' ' . $masterDevice->modelName;
                }
            }
        }

        $this->colorMfpReplacementDevices = $deviceArray;


        return $this->colorMfpReplacementDevices;
    }

    /**
     * Gets all reasons by the dealer id on the form
     *
     * @param $categoryId
     *
     * @return array
     */
    public function getDeviceSwapsByCategory ($categoryId)
    {
        $reasonArray = array();
        // Get the default reason
        $defaultReason                                    = DeviceSwapReasonDefaultMapper::getInstance()->findDefaultByDealerId($categoryId, $this->_dealerId);
        $reasonArray [$defaultReason->deviceSwapReasonId] = DeviceSwapReasonMapper::getInstance()->find($defaultReason->deviceSwapReasonId)->reason;

        foreach (DeviceSwapReasonMapper::getInstance()->fetchAllByCategoryId($categoryId, $this->_dealerId) as $reason)
        {
            // Add the element to the array as long as it's not the default since that is already added
            if (!array_key_exists($reason->id, $reasonArray))
            {
                $reasonArray[$reason->id] = $reason->reason;
            }
        }

        return $reasonArray;
    }
}