<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Services;

use MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\DeviceDbTable;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\AddDeviceForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\AddFavoriteDeviceForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use Zend_Form;

/**
 * Class BuildConfigurationService
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Services
 */
class BuildConfigurationService extends Zend_Form
{
    /**
     * The add device form
     *
     * @var AddDeviceForm
     */
    protected $_addDeviceForm;

    /**
     * The add favorite device form
     *
     * @var AddFavoriteDeviceForm
     */
    protected $_addFavoriteDeviceForm;

    /**
     * Gets a list of devices that match search string.
     * This function will search favorite device names, device names, and device skus to provide results.
     *
     * @param string $searchString
     *            The string being used in the search.
     *
     * @return DeviceModel[]
     */
    public function searchForDevice ($searchString)
    {
        // Search Favorite Devices and Devices
        $deviceDbTable = new DeviceDbTable();

        return $deviceDbTable->searchByNameOrSku($searchString);
    }

    /**
     * Gets the list of all available devices
     *
     * @return DeviceModel[]
     */
    public function getAllAvailableDevices ()
    {
        return DeviceMapper::getInstance()->fetchAll();
    }

    /**
     * Gets the list of all available favorite devices to a user
     *
     * @param int $userId
     *
     * @return DeviceConfigurationModel[]
     */
    public function getAllFavoriteDevicesForUser ($userId)
    {
        return DeviceConfigurationMapper::getInstance()->fetchAllDeviceConfigurationsAvailableToUser($userId);
    }

    /**
     *
     * @return AddDeviceForm
     */
    public function getAddDeviceForm ()
    {
        if (!isset($this->_addDeviceForm))
        {
            $this->_addDeviceForm = new AddDeviceForm();
        }

        return $this->_addDeviceForm;
    }

    /**
     *
     * @return AddFavoriteDeviceForm
     */
    public function getAddFavoriteDeviceForm ()
    {
        if (!isset($this->_addFavoriteDeviceForm))
        {
            $this->_addFavoriteDeviceForm = new AddFavoriteDeviceForm();
        }

        return $this->_addFavoriteDeviceForm;
    }
}

