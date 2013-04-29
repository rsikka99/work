<?php
class Healthcheck_Model_DeviceInstancesGroup
{
    /**
     * @var Proposalgen_Model_DeviceInstance []
     */
    protected $_deviceInstances = array();

    /**
     * @var Healthcheck_Model_PageCounts
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
        $this->_pageCounts = new Healthcheck_Model_PageCounts();
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
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
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
     * Removes a device from the devices array and from the pagecounts
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getDeviceInstances ()
    {
        return $this->_deviceInstances;
    }

    /**
     * Getter for _pageCounts
     *
     * @return \Healthcheck_Model_PageCounts
     */
    public function getPageCounts ()
    {
        return $this->_pageCounts;
    }


}