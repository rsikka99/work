<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use Zend_Form;
use Zend_Auth;

/**
 * Class AddDeviceForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class AddDeviceForm extends \My_Form_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // An add configuration button for the favorite devices
        $this->addElement('submit', 'addDevice', [
            'ignore' => true,
            'label'  => 'Add',
        ]);

        // Get configurations from database
        $devices = DeviceMapper::getInstance()->fetchQuoteDeviceListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);
        // Populate array with configurations
        $data        = [];
        $data ['-1'] = 'Select Device...';

        /* @var $device DeviceModel */
        foreach ($devices as $device)
        {
            $data [$device->masterDeviceId] = $device->getMasterDevice()->getFullDeviceName();
        }

        // This is a list of favorite devices that the user can add
        $this->addElement('select', 'masterDeviceId', [
            'label'        => 'Add Device',
            'multiOptions' => $data,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote/add-device-form.phtml']]]);
    }
}
