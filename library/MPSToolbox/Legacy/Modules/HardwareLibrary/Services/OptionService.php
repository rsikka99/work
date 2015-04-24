<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use MPSToolbox\Legacy\Modules\HardwareLibrary\Entities\OptionEntity;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteDeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;

/**
 * Class OptionService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 */
class OptionService
{
    /**
     * @var int
     */
    protected $dealerId;

    /**
     * @param int $dealerId
     */
    public function __construct ($dealerId)
    {
        $this->dealerId = $dealerId;
    }

    /**
     * Creates a option model
     *
     * @param array $data
     *
     * @return OptionEntity|bool
     */
    public function create ($data)
    {
        $optionEntity = new OptionEntity();
        $optionEntity->fill($data);
        $optionEntity->dealerId = $this->dealerId;

        if ($optionEntity->save())
        {
            return $optionEntity;
        }

        return false;
    }

    /**
     * Deletes options for the given ids
     *
     * Note: Checks dealerId passed into this service to ensure a match.
     *
     * @param array|int $ids
     *
     * @return int
     */
    public function delete ($ids)
    {
        return OptionEntity::whereIn('id', $ids)->where('dealerId', '=', $this->dealerId)->delete();
    }

    /**
     * Finds a option
     *
     * @param int $optionId
     *
     * @return OptionEntity
     */
    public function find ($optionId)
    {
        return OptionEntity::optionForDealer($optionId, $this->dealerId)->first();
    }

    /**
     * Updates an option
     *
     * Note: Checks dealerId passed into this service to ensure a match.
     *
     * @param int   $optionId
     * @param array $data
     *
     * @return bool
     */
    public function update ($optionId, $data)
    {
        $optionEntity = OptionEntity::optionForDealer($optionId, $this->dealerId)->first();

        if ($optionEntity instanceof OptionEntity && $optionEntity->dealerId == $this->dealerId)
        {
            if (array_key_exists('id', $data))
            {
                unset($data['id']);
            }

            $optionEntity->fill($data);

            if ($optionEntity->isDirty())
            {
                $optionEntity->save();
            }

            return $optionEntity;
        }

        return false;
    }

    /**
     * Handles mapping a option to a device
     *
     * @param int $optionId
     * @param int $masterDeviceId
     *
     * @throws \Exception
     */
    public function addToDevice ($optionId, $masterDeviceId)
    {
        // FIXME lrobert: Needs device option fields to be filled out
        throw new \Exception("Function not finished");

        $quoteDevice        = QuoteDeviceMapper::getInstance()->find($masterDeviceId);
        $option             = OptionMapper::getInstance()->find($optionId);
        $deviceOptionMapper = DeviceOptionMapper::getInstance();

        /**
         * Map The option
         */
        if ($quoteDevice instanceof QuoteDeviceModel && $option instanceof OptionModel)
        {
            $deviceOption = $deviceOptionMapper->find([$option->id, $quoteDevice->id]);
            if (!$deviceOption instanceof DeviceOptionModel)
            {
                $deviceOption                 = new DeviceOptionModel();
                $deviceOption->masterDeviceId = $quoteDevice->id;
                $deviceOption->optionId       = $option->id;

                $deviceOption->isSystemDevice = $this->isMasterHardwareAdministrator;

                $deviceOptionMapper->insert($deviceOption);
            }
        }
    }

    /**
     * Handles removing a option from a device
     *
     * @param int $optionId
     * @param int $masterDeviceId
     */
    public function removeFromDevice ($optionId, $masterDeviceId)
    {
        $deviceOption                 = new DeviceOptionModel();
        $deviceOption->optionId       = $optionId;
        $deviceOption->masterDeviceId = $masterDeviceId;
        DeviceOptionMapper::getInstance()->delete($deviceOption);
    }

    /**
     * Fetches a list of option entities
     *
     * @param array $optionIds
     *
     * @return OptionEntity[]
     */
    public function getOptions ($optionIds)
    {
        $options = [];

        $optionEntities = OptionEntity::find($optionIds);
        if ($optionEntities instanceof OptionEntity)
        {
            $options[] = $optionEntities;
        }
        else
        {
            foreach ($optionEntities as $optionEntity)
            {
                $options[] = $optionEntity;
            }
        }

        return $options;
    }
}