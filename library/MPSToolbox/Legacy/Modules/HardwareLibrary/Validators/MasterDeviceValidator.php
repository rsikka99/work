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
 * @Deprecated
 */
class MasterDeviceValidator
{
    /**
     * @param MasterDeviceModel $masterDeviceModel
     *
     * @return bool
     * @Deprecated
     */
    public static function validate ($masterDeviceModel)
    {
        throw new Exception('Deprecated');
    }

    /**
     * @param MasterDeviceModel $masterDeviceModel
     * @param TonerModel[]      $assignedToners
     *
     * @return bool|string[]
     * @Deprecated
     */
    public function validateAssignedToners ($masterDeviceModel, $assignedToners)
    {
        throw new Exception('Deprecated');
    }
}
