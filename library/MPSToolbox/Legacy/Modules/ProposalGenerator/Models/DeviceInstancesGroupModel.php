<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

/**
 * Class DeviceInstancesGroupModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class DeviceInstancesGroupModel
{
    /**
     * @var DeviceInstanceModel []
     */
    protected $_deviceInstances = array();

    /**
     * @var PageCountsModel
     */
    protected $_pageCounts;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * This is a constructor
     */
    public function __construct ()
    {
        $this->_pageCounts = new PageCountsModel();
    }

    /**
     * Getter for count
     *
     * @return int
     */
    public function getCount ()
    {
        return $this->count;
    }

    /**
     * Adds a single device to the devices array and to page counts
     *
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return $this
     */
    public function add ($deviceInstance)
    {
        $this->_deviceInstances[$deviceInstance->id] = $deviceInstance;
        $this->_pageCounts->add($deviceInstance->getPageCounts());
        $this->count++;

        return $this;
    }

    /**
     * Removes a device from the devices array and from the page counts
     *
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return $this
     */
    public function remove ($deviceInstance)
    {
        unset($this->_deviceInstances[$deviceInstance->id]);
        $this->_pageCounts->subtract($deviceInstance->getPageCounts());
        $this->count--;

        return $this;
    }

    /**
     * Gets the device instances
     *
     * @return DeviceInstanceModel[]
     */
    public function getDeviceInstances ()
    {
        return $this->_deviceInstances;
    }

    /**
     * Getter for _pageCounts
     *
     * @return PageCountsModel
     */
    public function getPageCounts ()
    {
        return $this->_pageCounts;
    }


}