<?php

/**
 * Quotgen_Service_AddDevice
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 *
 * @category PrintIQMPS
 * @package Quotegen_Service
 * @copyright Copyright (C) 2012 Tangent MTW Inc. <info@tangentmtw.com> (http://www.tangentmtw.com)
 *           
 */
class Quotegen_Service_BuildConfiguration extends Zend_Form
{
    
    /**
     * The add device form
     *
     * @var Quotegen_Form_AddDevice
     */
    protected $_addDeviceForm;
    
    /**
     * The add favorite device form
     *
     * @var Quotegen_Form_AddFavoriteDevice
     */
    protected $_addFavoriteDeviceForm;

    /**
     * Gets a list of devices that match search string.
     * This function will search favorite device names, device names, and device skus to provide results.
     *
     * @param string $searchString
     *            The string being used in the search.
     * @return unknown A list of search results
     */
    public function searchForDevice ($searchString)
    {
        // Search Favorite Devices and Devices
        $deviceDbTable = new Quotegen_Model_DbTable_Device();
        
        return $deviceDbTable->searchByNameOrSku($searchString);
    }

    /**
     * Gets the list of all available devices
     *
     * @return Ambigous <multitype:Quotegen_Model_Device, multitype:Quotegen_Model_Device >
     */
    public function getAllAvailableDevices ()
    {
        return Quotegen_Model_Mapper_Device::getInstance()->fetchAll();
    }

    /**
     * gets the list of all available favorite devices to a user
     *
     * @param int $userId            
     * @return Ambigous <multitype:Quotegen_Model_DeviceConfiguration, multitype:Quotegen_Model_DeviceConfiguration >
     */
    public function getAllFavoriteDevicesForUser ($userId)
    {
        return Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->fetchAllDeviceConfigurationsAvailableToUser($userId);
    }

    /**
     *
     * @return Quotegen_Form_AddDevice
     */
    public function getAddDeviceForm ()
    {
        if (! isset($this->_addDeviceForm))
        {
            $this->_addDeviceForm = new Quotegen_Form_AddDevice();
        }
        return $this->_addDeviceForm;
    }

    /**
     *
     * @return Quotegen_Form_AddFavoriteDevice
     */
    public function getAddFavoriteDeviceForm ()
    {
        if (! isset($this->_addFavoriteDeviceForm))
        {
            $this->_addFavoriteDeviceForm = new Quotegen_Form_AddFavoriteDevice();
        }
        return $this->_addFavoriteDeviceForm;
    }
}

