<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use Zend_Form;
use Zend_Auth;

/**
 * Class AddFavoriteDeviceForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class AddFavoriteDeviceForm extends \My_Form_Form
{
    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // An add configuration button for the favorite devices
        $this->addElement('submit', 'addDeviceConfiguration', [
            'ignore' => true,
            'label'  => 'Add',
        ]);

        // Get configurations from database
        $deviceConfigurations = DeviceConfigurationMapper::getInstance()->fetchDeviceConfigurationListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);

        // Populate array with configurations
        $data        = [];
        $data ['-1'] = 'Select Favorite...';

        /* @var $deviceConfiguration DeviceConfigurationModel */
        foreach ($deviceConfigurations as $deviceConfiguration)
        {
            $data [$deviceConfiguration->id] = $deviceConfiguration->name;
        }

        // This is a list of favorite devices that the user can add
        $this->addElement('select', 'deviceConfigurationId', [
            'label'        => 'Device Configuration',
            'multiOptions' => $data,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote/add-favorite-device-form.phtml']]]);
    }
}
