<?php

use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_DevicesController
 */
class HardwareLibrary_ServicesController extends \Tangent\Controller\Hardware_Action
{

    protected $hardware_type = 'services';
    protected $hardware_categories = ['General'];

    protected function getForms($hardwareService) {
        $result = parent::getForms($hardwareService);
        unset ($result['hardwareAttributes']);
        return $result;
    }

    /**
     * @param $hardwareId
     * @return \MPSToolbox\Entities\ExtHardwareEntity
     */
    public function getHardware($hardwareId, $createNew=false) {
        $hardware = \MPSToolbox\Entities\ExtServiceEntity::find($hardwareId);
        if (!$hardware && $createNew) $hardware = new \MPSToolbox\Entities\ExtServiceEntity();
        return $hardware;
    }
}
