<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use MPSToolbox\Legacy\Modules\HardwareLibrary\Validators\MasterDeviceValidator;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;

/**
 * Class MasterDeviceService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 */
class MasterDeviceService
{
    /**
     * Deletes a master device
     *
     * @param $masterDeviceId
     *
     * @return int
     */
    public function deleteMasterDevice ($masterDeviceId)
    {
        return MasterDeviceMapper::getInstance()->delete($masterDeviceId);
    }

    /**
     * Finds a master device
     *
     * @param $id
     *
     * @return MasterDeviceModel
     */
    public function findMasterDevice ($id)
    {
        return MasterDeviceMapper::getInstance()->find($id);
    }

    /**
     * @param      $data
     * @param null $id
     *
     * @return int|string
     */
    public function saveMasterDevice ($data, $id = null)
    {
        $masterDeviceModel = new MasterDeviceModel();
        $masterDeviceModel->populate($data);

        $validator = new MasterDeviceValidator();
        if ($validator->validate($masterDeviceModel))
        {
            if ($validator->validateAssignedToners($masterDeviceModel, $toners))
            {
                if ($id > 0)
                {
                    $masterDeviceModel->id = $id;

                    return MasterDeviceMapper::getInstance()->save($masterDeviceModel);
                }
                else
                {
                    return MasterDeviceMapper::getInstance()->insert($masterDeviceModel);
                }
            }
        }
    }

    /**
     * Searches for a master device and returns a list of matches
     *
     * @param $searchTerm
     *
     * @return array
     */
    public function searchForMasterDevice ($searchTerm)
    {
        $results            = [];
        $masterDeviceMapper = MasterDeviceMapper::getInstance();

        if ($searchTerm !== false)
        {
            foreach ($masterDeviceMapper->searchByName($searchTerm) as $masterDeviceSearchResult)
            {
                $results[] = [
                    "id"   => $masterDeviceSearchResult->masterDeviceId,
                    "text" => $masterDeviceSearchResult->masterDeviceFullDeviceName
                ];
            }
        }

        return $results;
    }
}
