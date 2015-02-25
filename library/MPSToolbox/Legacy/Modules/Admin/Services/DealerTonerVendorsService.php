<?php
namespace MPSToolbox\Legacy\Modules\Admin\Services;

use MPSToolbox\Legacy\Modules\Admin\Mappers\DealerTonerVendorMapper;
use MPSToolbox\Legacy\Modules\Admin\Models\DealerTonerVendorModel;

/**
 * Class DealerTonerVendorsService
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Services
 */
class DealerTonerVendorsService extends \Tangent\Service\BaseService
{
    const ERROR_TONER_VENDOR_CREATE_FAIL = 'ERROR_TONER_VENDOR_CREATE_FAIL';
    const ERROR_TONER_VENDOR_DELETE_FAIL = 'ERROR_TONER_VENDOR_DELETE_FAIL';

    /**
     * Updates a dealers toner vendors
     *
     * @param int   $dealerId
     * @param array $data
     */
    public function updateDealerTonerVendors ($dealerId, $data)
    {
        $currentTonerVendorManufacturerIds = $this->getDealerTonerVendorsAsArray($dealerId);
        $dealerTonerVendorMapper           = DealerTonerVendorMapper::getInstance();

        if (isset($data['manufacturerIds']))
        {
            foreach ($data['manufacturerIds'] as $newManufacturerId)
            {
                $create = true;
                foreach ($currentTonerVendorManufacturerIds as $oldManufacturerId)
                {
                    if ((int)$newManufacturerId === (int)$oldManufacturerId)
                    {
                        $create = false;
                        break;
                    }
                }

                if ($create)
                {
                    $dealerTonerVendor                 = new DealerTonerVendorModel();
                    $dealerTonerVendor->dealerId       = $dealerId;
                    $dealerTonerVendor->manufacturerId = $newManufacturerId;
                    $dealerTonerVendorMapper->insert($dealerTonerVendor);
                }
            }

            foreach ($currentTonerVendorManufacturerIds as $oldManufacturerId)
            {
                $delete = true;
                foreach ($data['manufacturerIds'] as $newManufacturerId)
                {
                    if ((int)$newManufacturerId === (int)$oldManufacturerId)
                    {
                        $delete = false;
                        break;
                    }
                }

                if ($delete)
                {
                    $dealerTonerVendorMapper->delete([(int)$dealerId, $oldManufacturerId]);
                }
            }
        }
        else
        {
            foreach ($currentTonerVendorManufacturerIds as $oldManufacturerId)
            {
                $dealerTonerVendorMapper->delete([(int)$dealerId, $oldManufacturerId]);
            }
        }
    }

    /**
     * Gets the toner vendor ids for a given dealer
     *
     * @param int $dealerId
     *
     * @return array
     */
    public function getDealerTonerVendorsAsArray ($dealerId)
    {
        $tonerVendorArray = [];

        foreach (DealerTonerVendorMapper::getInstance()->fetchAllForDealer($dealerId) as $dealerTonerVendor)
        {
            $tonerVendorArray[] = $dealerTonerVendor->manufacturerId;
        }

        return $tonerVendorArray;
    }

}