<?php

use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_DevicesController
 */
class HardwareLibrary_ComputersController extends \Tangent\Controller\Hardware_Action
{
    /**
     * @param $hardwareId
     * @return \MPSToolbox\Entities\ExtHardwareEntity
     */
    public function getHardware($hardwareId, $createNew=false) {
        $hardware = \MPSToolbox\Entities\ExtComputerEntity::find($hardwareId);
        if (!$hardware && $createNew) $hardware = new \MPSToolbox\Entities\ExtComputerEntity();
        return $hardware;
    }
}
