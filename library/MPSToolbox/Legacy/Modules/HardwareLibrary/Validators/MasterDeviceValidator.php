<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Validators;

use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerConfigMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;

/**
 * Class MasterDeviceValidator
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Validators
 */
class MasterDeviceValidator
{
    /**
     * @param MasterDeviceModel $masterDeviceModel
     *
     * @return bool
     */
    public static function validate ($masterDeviceModel)
    {
        $validationMessages = array();

        /**
         * User ids are allowed to be null
         */
        if (is_int($masterDeviceModel->userId) && !UserMapper::getInstance()->exists($masterDeviceModel->userId))
        {
            $validationMessages[] = 'Invalid user id';
        }

        /**
         * Devices require a manufacturer
         */
        if (!is_int($masterDeviceModel->manufacturerId) || !ManufacturerMapper::getInstance()->exists($masterDeviceModel->manufacturerId))
        {
            $validationMessages[] = 'Invalid manufacturer id';
        }

        /**
         * Devices require a model name
         */
        if (strlen($masterDeviceModel->modelName) < 1)
        {
            $validationMessages[] = 'Invalid model name';
        }

        /**
         * Devices require a toner configuration
         */
        if (!is_int($masterDeviceModel->tonerConfigId) || !TonerConfigMapper::getInstance()->exists($masterDeviceModel->tonerConfigId))
        {
            $validationMessages[] = 'Invalid toner configuration id';
        }

        if (isset($masterDeviceModel) && $masterDeviceModel->wattsPowerNormal <= 0)
        {
            $validationMessages[] = 'Invalid operating wattage (watts power normal)';
        }

        if (isset($masterDeviceModel) && $masterDeviceModel->wattsPowerIdle <= 0)
        {
            $validationMessages[] = 'Invalid idle wattage (watts power idle)';
        }

        if (!isset($masterDeviceModel->launchDate))
        {
            $validationMessages[] = 'Launch date is required';
        }

        if ($masterDeviceModel->dateCreated == 0)
        {
            $validationMessages[] = 'Invalid dateCreated';
        }

        if ($masterDeviceModel->ppmBlack == 0)
        {
            $validationMessages[] = 'Invalid ppmBlack';
        }

        if ($masterDeviceModel->ppmColor == 0)
        {
            $validationMessages[] = 'Invalid ppmColor';
        }

        if ($masterDeviceModel->isLeased == 0)
        {
            $validationMessages[] = 'Invalid isLeased';
        }

        if ($masterDeviceModel->leasedTonerYield == 0)
        {
            $validationMessages[] = 'Invalid leasedTonerYield';
        }

        if ($masterDeviceModel->isCapableOfReportingTonerLevels == 0)
        {
            $validationMessages[] = 'Invalid isCapableOfReportingTonerLevels';
        }

        if ($masterDeviceModel->calculatedLaborCostPerPage == 0)
        {
            $validationMessages[] = 'Invalid calculatedLaborCostPerPage';
        }

        if ($masterDeviceModel->isUsingDealerLaborCostPerPage == 0)
        {
            $validationMessages[] = 'Invalid isUsingDealerLaborCostPerPage';
        }

        if ($masterDeviceModel->isUsingReportLaborCostPerPage == 0)
        {
            $validationMessages[] = 'Invalid isUsingReportLaborCostPerPage';
        }

        if ($masterDeviceModel->calculatedPartsCostPerPage == 0)
        {
            $validationMessages[] = 'Invalid calculatedPartsCostPerPage';
        }

        if ($masterDeviceModel->isUsingDealerPartsCostPerPage == 0)
        {
            $validationMessages[] = 'Invalid isUsingDealerPartsCostPerPage';
        }

        if ($masterDeviceModel->isUsingReportPartsCostPerPage == 0)
        {
            $validationMessages[] = 'Invalid isUsingReportPartsCostPerPage';
        }

        if (count($validationMessages) > 0)
        {
            return $validationMessages;
        }

        return true;
    }

    /**
     * @param MasterDeviceModel $masterDeviceModel
     * @param TonerModel[]      $assignedToners
     *
     * @return bool|string[]
     */
    public function validateAssignedToners ($masterDeviceModel, $assignedToners)
    {
        $validationErrorMessages = [];


        $assignedOemTonerColors = array(
            TonerColorModel::BLACK       => false,
            TonerColorModel::CYAN        => false,
            TonerColorModel::MAGENTA     => false,
            TonerColorModel::YELLOW      => false,
            TonerColorModel::THREE_COLOR => false,
            TonerColorModel::FOUR_COLOR  => false,
        );

        $assignedTonerColors = array(
            TonerColorModel::BLACK       => false,
            TonerColorModel::CYAN        => false,
            TonerColorModel::MAGENTA     => false,
            TonerColorModel::YELLOW      => false,
            TonerColorModel::THREE_COLOR => false,
            TonerColorModel::FOUR_COLOR  => false,
        );

        /**
         * Figure out what color and type of toners we have
         */
        foreach ($assignedToners as $toner)
        {
            $assignedTonerColors[(int)$toner->tonerColorId] = true;

            /**
             * OEM toners have the same manufacturer id as the device
             * Devices are required to have a single set of OEM toners at all
             * times
             */
            if ((int)$toner->manufacturerId == (int)$masterDeviceModel->manufacturerId)
            {
                $assignedOemTonerColors[(int)$toner['tonerColorId']] = true;
            }
        }

        $tonerConfigurationColors = TonerConfigModel::getRequiredTonersForTonerConfig($tonerConfigId);

        /**
         * Devices require at least one full set of OEM toners for a given color set.
         */
        foreach ($tonerConfigurationColors as $requiredTonerColorId)
        {
            if (!$assignedOemTonerColors[$requiredTonerColorId])
            {
                // Missing a required toner color
                $validationErrorMessages[] = sprintf('Missing %1$s OEM Toner.', TonerColorModel::$ColorNames[$requiredTonerColorId]);
            }
        }

        /**
         * Some devices cannot be assigned certain colors (IE Black devices can only have black toners)
         */
        foreach ($assignedTonerColors as $assignedTonerColorId => $isAssigned)
        {
            if ($isAssigned && !in_array($assignedTonerColorId, $tonerConfigurationColors))
            {
                // Invalid Toner Color assigned to the device
                $validationErrorMessages[] = sprintf('%1$s Toners cannot be assigned to this device.', TonerColorModel::$ColorNames[$assignedTonerColorId]);
            }
        }

        if (count($validationErrorMessages) > 0)
        {
            return implode(' ', $validationErrorMessages);
        }
        else
        {
            return true;
        }
    }
}
