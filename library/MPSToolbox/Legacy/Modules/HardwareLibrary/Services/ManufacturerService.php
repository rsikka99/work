<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorManufacturerModel;
use Tangent\Service\BaseService;

class ManufacturerService extends BaseService
{
    const ERROR_CREATE_FAIL              = 'ERROR_CREATE_FAIL';
    const ERROR_UPDATE_FAIL              = 'ERROR_UPDATE_FAIL';
    const ERROR_DELETE_FAIL              = 'ERROR_DELETE_FAIL';
    const ERROR_TONER_VENDOR_CREATE_FAIL = 'ERROR_TONER_VENDOR_CREATE_FAIL';
    const ERROR_TONER_VENDOR_DELETE_FAIL = 'ERROR_TONER_VENDOR_DELETE_FAIL';

    /**
     * Creates a manufacturer
     *
     * @param array $data
     *
     * @return ManufacturerModel
     */
    public function createManufacturer ($data)
    {
        $manufacturer   = new ManufacturerModel($data);
        $manufacturerId = ManufacturerMapper::getInstance()->insert($manufacturer);

        if ($manufacturerId)
        {
            if ($manufacturerId && $data['isTonerVendor'])
            {
                $this->addManufacturerAsTonerVendor($manufacturerId);
            }
        }
        else
        {
            $this->addError(self::ERROR_CREATE_FAIL, 'Error creating manufacturer');
        }

        return $manufacturer;
    }

    /**
     * @param array $data
     * @param int   $manufacturerId
     */
    public function saveManufacturer ($data, $manufacturerId)
    {
        $mapper       = ManufacturerMapper::getInstance();
        $manufacturer = $mapper->find($manufacturerId);

        if ($manufacturer instanceof ManufacturerModel)
        {
            unset($data['id']);
            $manufacturer->populate($data);
            $mapper->save($manufacturer);

            if ($data['isTonerVendor'] && !$manufacturer->isTonerVendor())
            {
                $this->addManufacturerAsTonerVendor($manufacturerId);
            }
            else if (!$data['isTonerVendor'] && $manufacturer->isTonerVendor())
            {
                $this->deleteManufacturerAsTonerVendor($manufacturerId);
            }
        }
    }

    /**
     * Deletes a manufacturer
     *
     * @param int|ManufacturerModel $object
     */
    public function deleteManufacturer ($object)
    {
        ManufacturerMapper::getInstance()->delete($object);
    }

    /**
     * Adds a manufacturer from the compatible toner vendor list
     *
     * @param $manufacturerId
     */
    public function addManufacturerAsTonerVendor ($manufacturerId)
    {
        $tonerVendorManufacturer                 = new TonerVendorManufacturerModel();
        $tonerVendorManufacturer->manufacturerId = $manufacturerId;
        $tonerVendorManufacturerId               = TonerVendorManufacturerMapper::getInstance()->insert($tonerVendorManufacturer);
        if (!$tonerVendorManufacturerId)
        {
            $this->addError(self::ERROR_TONER_VENDOR_CREATE_FAIL, 'Error creating manufacturer');
        }
    }

    /**
     * Removes a manufacturer from the compatible toner vendor list
     *
     * @param $manufacturerId
     */
    public function deleteManufacturerAsTonerVendor ($manufacturerId)
    {
        TonerVendorManufacturerMapper::getInstance()->delete($manufacturerId);
    }

    /**
     * Searches for a manufacturer and returns a list of matches
     *
     * @param $searchTerm
     *
     * @return array
     */
    public function searchForManufacturer ($searchTerm)
    {
        $results = array();

        if ($searchTerm !== false)
        {
            $manufacturerMapper = ManufacturerMapper::getInstance();

            foreach ($manufacturerMapper->searchByName($searchTerm) as $manufacturer)
            {
                $results[] = $manufacturer;
            }
        }

        return $results;
    }
}