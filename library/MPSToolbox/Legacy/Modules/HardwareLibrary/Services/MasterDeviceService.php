<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use MPSToolbox\Legacy\Modules\HardwareLibrary\Validators\MasterDeviceValidator;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use PhpOffice\PhpWord\Exception\Exception;

/**
 * Class MasterDeviceService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 * @Deprecated
 */
class MasterDeviceService
{
    /**
     * Deletes a master device
     *
     * @param $masterDeviceId
     *
     * @return int
     * @Deprecated
     */
    public function deleteMasterDevice ($masterDeviceId)
    {
        throw new Exception('Deprecated');
    }

    /**
     * Finds a master device
     *
     * @param $id
     *
     * @return MasterDeviceModel
     * @Deprecated
     */
    public function findMasterDevice ($id)
    {
        throw new Exception('Deprecated');
    }

    /**
     * @param      $data
     * @param null $id
     *
     * @return int|string
     * @Deprecated
     */
    public function saveMasterDevice ($data, $id = null)
    {
        throw new Exception('Deprecated');
    }

    /**
     * Searches for a master device and returns a list of matches
     *
     * @param $searchTerm
     *
     * @return array
     * @Deprecated
     */
    public function searchForMasterDevice ($searchTerm)
    {
        throw new Exception('Deprecated');
    }
}
