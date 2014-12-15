<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;

/**
 * Interface OptimizationDeviceReplacementInterface
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
interface OptimizationDeviceReplacementInterface
{
    /**
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return MasterDeviceModel
     */
    public function findReplacement ($deviceInstance);
}