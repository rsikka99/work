<?php

use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_DevicesController
 */
class HardwareLibrary_PeripheralsController extends \Tangent\Controller\Hardware_Action
{

    protected $hardware_type = 'Peripherals';
    protected $hardware_categories = ['Monitor','External Drive'];

    /**
     * @param $hardwareId
     * @return \MPSToolbox\Entities\ExtHardwareEntity
     */
    public function getHardware($hardwareId, $createNew=false) {
        $hardware = \MPSToolbox\Entities\ExtPeripheralEntity::find($hardwareId);
        if (!$hardware && $createNew) $hardware = new \MPSToolbox\Entities\ExtPeripheralEntity();
        return $hardware;
    }
}
